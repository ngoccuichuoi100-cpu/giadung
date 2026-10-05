<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use App\Services\ProductRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SearchHistoryController extends Controller
{
    protected ProductRecommendationService $recommendationService;

    public function __construct(ProductRecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get search history and trending searches for the current user/session.
     */
    public function index(Request $request)
    {
        $userId = Auth::check() ? Auth::id() : null;
        $sessionId = $this->getSessionId($request);

        $recent = SearchHistory::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId)->orWhere('session_id', $sessionId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
            ->orderByDesc('updated_at')
            ->take(8)
            ->get(['id', 'keyword', 'updated_at'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'keyword' => $item->keyword,
                    'time_ago' => $item->updated_at ? $item->updated_at->diffForHumans() : 'Vừa xong',
                ];
            });

        // Get trending search keywords from all records in the last 30 days
        $trendingFromDb = SearchHistory::select('keyword', DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('keyword')
            ->orderByDesc('total')
            ->take(6)
            ->pluck('keyword')
            ->toArray();

        // Default popular keywords if not enough search data
        $defaultTrending = [
            'Nồi chiên không dầu',
            'Nồi cơm điện',
            'Máy xay sinh tố',
            'Nồi lẩu điện',
            'Chảo chống dính',
            'Bình đun siêu tốc',
        ];

        $trending = array_values(array_unique(array_merge($trendingFromDb, $defaultTrending)));
        $trending = array_slice($trending, 0, 6);

        return response()->json([
            'success' => true,
            'recent' => $recent,
            'trending' => $trending,
        ]);
    }

    /**
     * Store a search keyword in history.
     */
    public function store(Request $request)
    {
        $keyword = trim((string) $request->input('keyword', ''));
        if (empty($keyword) || mb_strlen($keyword) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Từ khóa tìm kiếm quá ngắn (tối thiểu 2 ký tự)',
            ], 422);
        }

        $resultsCount = (int) $request->input('results_count', 0);
        $history = $this->recommendationService->recordSearch($keyword, $resultsCount, $request);

        return response()->json([
            'success' => true,
            'item' => $history ? [
                'id' => $history->id,
                'keyword' => $history->keyword,
                'time_ago' => $history->updated_at ? $history->updated_at->diffForHumans() : 'Vừa xong',
            ] : null,
        ]);
    }

    /**
     * Delete a specific search history item (by ID or keyword).
     */
    public function destroy(Request $request, $id)
    {
        $userId = Auth::check() ? Auth::id() : null;
        $sessionId = $this->getSessionId($request);

        $query = SearchHistory::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId)->orWhere('session_id', $sessionId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId));

        if (is_numeric($id)) {
            $query->where('id', $id);
        } else {
            $decodedKeyword = urldecode((string) $id);
            $query->where('keyword', $decodedKeyword);
        }

        $deleted = $query->delete();

        return response()->json([
            'success' => (bool)$deleted,
            'message' => $deleted ? 'Đã xóa khỏi lịch sử tìm kiếm' : 'Không tìm thấy mục cần xóa',
        ]);
    }

    /**
     * Clear all search history for the current user/session.
     */
    public function clear(Request $request)
    {
        $userId = Auth::check() ? Auth::id() : null;
        $sessionId = $this->getSessionId($request);

        SearchHistory::query()
            ->when($userId, fn($q) => $q->where('user_id', $userId)->orWhere('session_id', $sessionId))
            ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa toàn bộ lịch sử tìm kiếm',
        ]);
    }

    /**
     * Safely resolve session ID or fallback identifier.
     */
    private function getSessionId(Request $request): string
    {
        return $request->hasSession() ? $request->session()->getId() : 'guest_' . md5($request->ip() ?? 'guest');
    }
}
