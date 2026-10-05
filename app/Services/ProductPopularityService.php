<?php

namespace App\Services;

use App\Models\Product;
use App\Models\OrderItem;
use App\Models\ProductView;
use App\Models\CartItem;
use App\Models\Wishlist;
use App\Models\ProductReview;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class ProductPopularityService
{
    protected $settingsFile = 'settings.json';

    public function getWeights(): array
    {
        if (Storage::exists($this->settingsFile)) {
            $data = json_decode(Storage::get($this->settingsFile), true);
            if (isset($data['popularity_weights'])) {
                return $data['popularity_weights'];
            }
        }

        // Default weights
        return [
            'sales' => 5.0,
            'views' => 1.0,
            'cart_adds' => 3.0,
            'wishlists' => 4.0,
            'rating' => 10.0,
        ];
    }

    public function saveWeights(array $weights): void
    {
        $data = [];
        if (Storage::exists($this->settingsFile)) {
            $data = json_decode(Storage::get($this->settingsFile), true) ?? [];
        }

        $data['popularity_weights'] = array_merge($this->getWeights(), $weights);
        Storage::put($this->settingsFile, json_encode($data, JSON_PRETTY_PRINT));
        
        Cache::forget('popular_products_8');
        Cache::forget('popular_products_12');
        Cache::forget('popular_products_16');
    }

    /**
     * Get popular products sorted by the custom popularity formula.
     */
    public function getPopularProducts(int $limit = 8)
    {
        return Cache::remember('popular_products_' . $limit, 300, function () use ($limit) {
            $weights = $this->getWeights();

            // Bulk aggregate queries to eliminate N+1 loop performance bottleneck
            $salesData = OrderItem::whereHas('order', function ($q) {
                    $q->whereNotIn('order_status', ['cancelled']);
                })
                ->selectRaw('product_id, SUM(quantity) as total_sales')
                ->groupBy('product_id')
                ->pluck('total_sales', 'product_id');

            $viewsData = ProductView::selectRaw('product_id, COUNT(*) as total_views')
                ->groupBy('product_id')
                ->pluck('total_views', 'product_id');

            $cartAddsData = CartItem::selectRaw('product_id, SUM(quantity) as total_cart_adds')
                ->groupBy('product_id')
                ->pluck('total_cart_adds', 'product_id');

            $wishlistsData = Wishlist::selectRaw('product_id, COUNT(*) as total_wishlists')
                ->groupBy('product_id')
                ->pluck('total_wishlists', 'product_id');

            $ratingsData = ProductReview::where('status', 'approved')
                ->selectRaw('product_id, AVG(rating) as avg_rating')
                ->groupBy('product_id')
                ->pluck('avg_rating', 'product_id');

            $products = Product::where('status', 'active')->with(['category', 'variants'])->get();

            $scoredProducts = $products->map(function ($product) use ($weights, $salesData, $viewsData, $cartAddsData, $wishlistsData, $ratingsData) {
                $salesCount = (int)($salesData->get($product->id) ?? 0);
                $viewsCount = (int)($viewsData->get($product->id) ?? 0);
                $cartAddsCount = (int)($cartAddsData->get($product->id) ?? 0);
                $wishlistCount = (int)($wishlistsData->get($product->id) ?? 0);
                $avgRating = (float)($ratingsData->get($product->id) ?? 0.0);

                // Score formula
                $score = ($salesCount * $weights['sales'])
                    + ($viewsCount * $weights['views'])
                    + ($cartAddsCount * $weights['cart_adds'])
                    + ($wishlistCount * $weights['wishlists'])
                    + ($avgRating * $weights['rating']);

                $product->popularity_score = $score;
                $product->sales_count = $salesCount;
                $product->views_count = $viewsCount;
                $product->avg_rating = $avgRating;

                return $product;
            });

            // Sort descending by score
            return $scoredProducts->sortByDesc('popularity_score')->take($limit);
        });
    }
}
