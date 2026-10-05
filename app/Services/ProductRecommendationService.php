<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SearchHistory;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductRecommendationService
{
    protected ProductPopularityService $popularityService;

    public function __construct(ProductPopularityService $popularityService)
    {
        $this->popularityService = $popularityService;
    }

    /**
     * Log a user search query.
     */
    public function recordSearch(string $keyword, int $resultsCount, Request $request): ?SearchHistory
    {
        $cleanKeyword = trim($keyword);
        if (empty($cleanKeyword) || mb_strlen($cleanKeyword) < 2) {
            return null;
        }

        $userId = Auth::check() ? Auth::id() : null;
        $sessionId = $this->getSessionId($request);
        $ip = $request->ip();

        // Update if recent record exists with the same keyword to avoid duplicate spam, or create new
        $existing = SearchHistory::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId)->orWhere('session_id', $sessionId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
            ->where('keyword', $cleanKeyword)
            ->first();

        if ($existing) {
            $updateData = [
                'updated_at' => now(),
            ];
            if ($resultsCount > 0) {
                $updateData['results_count'] = $resultsCount;
            }
            if ($userId && !$existing->user_id) {
                $updateData['user_id'] = $userId;
            }
            $existing->update($updateData);
            $history = $existing;
        } else {
            $history = SearchHistory::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'ip_address' => $ip,
                'keyword' => $cleanKeyword,
                'results_count' => $resultsCount,
            ]);
        }

        // Limit user's history records to maximum 20 items to keep DB clean
        $historyQuery = SearchHistory::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId)->orWhere('session_id', $sessionId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId));

        $count = $historyQuery->count();
        if ($count > 20) {
            $oldIds = (clone $historyQuery)
                ->orderBy('updated_at', 'asc')
                ->take($count - 20)
                ->pluck('id');
            SearchHistory::whereIn('id', $oldIds)->delete();
        }

        return $history;
    }

    /**
     * Get recent distinct search keywords for the current user or guest.
     */
    public function getRecentKeywords(Request $request, int $limit = 5): array
    {
        $userId = Auth::check() ? Auth::id() : null;
        $sessionId = $this->getSessionId($request);

        return SearchHistory::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId)->orWhere('session_id', $sessionId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
            ->orderByDesc('updated_at')
            ->pluck('keyword')
            ->unique()
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Get product recommendations based on search history.
     * Fallbacks to popular products if no search history is present.
     */
    public function getRecommendations(Request $request, int $limit = 8, ?int $excludeProductId = null): array
    {
        $keywords = $this->getRecentKeywords($request, 5);

        if (!empty($keywords)) {
            // Find categories and brands related to keywords
            $categoryIds = Category::where('status', 'active')
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $query->orWhere('name', 'like', "%{$kw}%");
                    }
                })->pluck('id')->toArray();

            $brandIds = Brand::where('status', 'active')
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $query->orWhere('name', 'like', "%{$kw}%");
                    }
                })->pluck('id')->toArray();

            // Query matching products
            $productsQuery = Product::where('status', 'active')
                ->when($excludeProductId, fn($q) => $q->where('id', '!=', $excludeProductId))
                ->where(function ($query) use ($keywords, $categoryIds, $brandIds) {
                    // Match product name or code
                    $query->where(function ($sub) use ($keywords) {
                        foreach ($keywords as $kw) {
                            $sub->orWhere('name', 'like', "%{$kw}%")
                                ->orWhere('code', 'like', "%{$kw}%")
                                ->orWhere('short_description', 'like', "%{$kw}%");
                        }
                    });

                    // Or match category / brand
                    if (!empty($categoryIds)) {
                        $query->orWhereIn('category_id', $categoryIds);
                    }
                    if (!empty($brandIds)) {
                        $query->orWhereIn('brand_id', $brandIds);
                    }
                })
                ->orderByRaw('stock_quantity > 0 DESC')
                ->latest()
                ->take($limit);

            $matchedProducts = $productsQuery->get();

            // If matched products are fewer than requested limit, supplement with popular items
            if ($matchedProducts->count() < $limit) {
                $needed = $limit - $matchedProducts->count();
                $existingIds = $matchedProducts->pluck('id')->toArray();
                if ($excludeProductId) {
                    $existingIds[] = $excludeProductId;
                }

                $popularSupplement = Product::where('status', 'active')
                    ->whereNotIn('id', $existingIds)
                    ->where('stock_quantity', '>', 0)
                    ->latest()
                    ->take($needed)
                    ->get();

                $finalProducts = $matchedProducts->concat($popularSupplement);
            } else {
                $finalProducts = $matchedProducts;
            }

            return [
                'products' => $finalProducts,
                'keywords' => $keywords,
                'has_history' => true,
            ];
        }

        // Fallback: No search history yet -> return popular/featured products
        $fallbackProducts = $this->popularityService->getPopularProducts($limit);
        if ($fallbackProducts->isEmpty()) {
            $fallbackProducts = Product::where('status', 'active')
                ->when($excludeProductId, fn($q) => $q->where('id', '!=', $excludeProductId))
                ->latest()
                ->take($limit)
                ->get();
        }

        return [
            'products' => $fallbackProducts,
            'keywords' => [],
            'has_history' => false,
        ];
    }

    /**
     * Safely resolve session ID or fallback identifier.
     */
    private function getSessionId(Request $request): string
    {
        return $request->hasSession() ? $request->session()->getId() : 'guest_' . md5($request->ip() ?? 'guest');
    }
}
