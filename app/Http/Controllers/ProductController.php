<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;
use App\Models\Warranty;
use App\Models\ProductReview;
use App\Models\ReviewVote;
use App\Models\ProductView;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\ProductRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

class ProductController extends Controller
{
    public function index(Request $request, ProductRecommendationService $recommendationService)
    {
        $query = Product::where('status', 'active');

        // Search by name or code
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            });
        }

        // Filter by category slug or ID
        if ($request->filled('category')) {
            $categorySlug = $request->category;
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Filter by brand slug or ID
        if ($request->filled('brand')) {
            $brandSlug = $request->brand;
            $brand = \App\Models\Brand::where('slug', $brandSlug)->first();
            if ($brand) {
                $query->where('brand_id', $brand->id);
            }
        }

        // Filter by price range
        if ($request->filled('price_min')) {
            $query->whereRaw('COALESCE(sale_price, price) >= ?', [$request->price_min]);
        }
        if ($request->filled('price_max')) {
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [$request->price_max]);
        }

        // Filter by stock (in stock only)
        if ($request->boolean('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        // Filter: Hàng nội địa vs hàng ngoại nhập
        if ($request->filled('origin')) {
            if ($request->origin === 'domestic') {
                $query->where('is_domestic', true);
            } elseif ($request->origin === 'imported') {
                $query->where('is_domestic', false);
            }
        }

        // Sort & Filters
        $sort = $request->get('sort', 'latest');
        if ($sort === 'price_asc') {
            $query->orderByRaw('COALESCE(sale_price, price) ASC');
        } elseif ($sort === 'price_desc') {
            $query->orderByRaw('COALESCE(sale_price, price) DESC');
        } elseif ($sort === 'best_sellers') {
            // Sort by sales count from order_items
            $query->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                ->select('products.*', DB::raw('COALESCE(SUM(order_items.quantity), 0) as sales_volume'))
                ->groupBy('products.id')
                ->orderByDesc('sales_volume');
        } elseif ($sort === 'top_rated' || $sort === 'best_quality') {
            // Sort by average approved reviews rating
            $query->leftJoin('product_reviews', function($join) {
                $join->on('products.id', '=', 'product_reviews.product_id')
                     ->where('product_reviews.status', '=', 'approved');
            })
            ->select('products.*', DB::raw('COALESCE(AVG(product_reviews.rating), 0) as average_rating'))
            ->groupBy('products.id')
            ->orderByDesc('average_rating');
        } elseif ($sort === 'famous_brands') {
            // Famous brands products
            $query->whereHas('brandRelation', function($q) {
                $q->where('is_featured', true);
            })->latest();
        } else {
            // Default sorting: If search query 'q' is present, rank by relevance first!
            if ($request->filled('q')) {
                $searchQ = $request->q;
                $query->orderByRaw("
                    CASE 
                        WHEN products.name LIKE ? THEN 1
                        WHEN products.name LIKE ? THEN 2
                        WHEN products.brand LIKE ? THEN 3
                        WHEN products.code LIKE ? THEN 4
                        ELSE 5
                    END ASC, products.created_at DESC
                ", [
                    "{$searchQ}%",
                    "% {$searchQ}%",
                    "{$searchQ}%",
                    "{$searchQ}%",
                ]);
            } else {
                $query->latest('products.created_at');
            }
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('status', 'active')->get();
        $brands = \App\Models\Brand::where('status', 'active')->get();

        // 1. Record search history if user searched for a keyword
        if ($request->filled('q')) {
            $recommendationService->recordSearch($request->q, $products->total(), $request);
        }

        // 2. Fetch recommended products based on search history (or popular fallbacks)
        $recommendationData = $recommendationService->getRecommendations($request, 4);
        $recommendedProducts = $recommendationData['products'];
        $recommendedKeywords = $recommendationData['keywords'];
        $hasSearchHistory = $recommendationData['has_history'];

        // Return partial JSON for AJAX live filter
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'total' => $products->total(),
                'html' => view('products.partials.grid', compact('products'))->render(),
            ]);
        }

        return view('products.index', compact(
            'products',
            'categories',
            'brands',
            'recommendedProducts',
            'recommendedKeywords',
            'hasSearchHistory'
        ));
    }

    /**
     * Realtime Live Search API for instant dropdown suggestions
     */
    public function liveSearch(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (mb_strlen($q) < 1) {
            return response()->json([
                'success' => true,
                'products' => [],
                'total' => 0
            ]);
        }

        $query = Product::where('status', 'active')
            ->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('brand', 'like', "%{$q}%");
            })
            ->with(['category', 'brandRelation'])
            ->orderByRaw("
                CASE 
                    WHEN name LIKE ? THEN 1
                    WHEN name LIKE ? THEN 2
                    WHEN brand LIKE ? THEN 3
                    WHEN code LIKE ? THEN 4
                    ELSE 5
                END ASC, id ASC
            ", [
                "{$q}%",
                "% {$q}%",
                "{$q}%",
                "{$q}%",
            ]);

        $total = $query->count();
        $products = $query->limit(6)->get()->map(function ($product) {
            $price = (float) $product->price;
            $salePrice = ($product->sale_price !== null && $product->sale_price < $product->price) ? (float) $product->sale_price : null;
            $discountPercent = $salePrice ? round((($price - $salePrice) / $price) * 100) : 0;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'slug' => $product->slug,
                'url' => route('products.show', $product->slug),
                'thumbnail' => $product->thumbnail_url,
                'price_formatted' => number_format($price, 0, ',', '.') . '₫',
                'sale_price_formatted' => $salePrice ? number_format($salePrice, 0, ',', '.') . '₫' : null,
                'discount_percent' => $discountPercent,
                'category' => $product->category?->name,
                'brand' => $product->brand ?: $product->brandRelation?->name,
                'in_stock' => $product->stock_quantity > 0,
            ];
        });

        return response()->json([
            'success' => true,
            'products' => $products,
            'total' => $total,
            'view_all_url' => route('products.index', ['q' => $q]),
        ]);
    }

    public function show(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'active')
            ->with(['category', 'images', 'brandRelation', 'warranties'])
            ->firstOrFail();

        // 1. Record view count (Product popularity - throttled by session to prevent DB write overload on refresh)
        $viewedKey = 'viewed_product_' . $product->id;
        if (!session()->has($viewedKey)) {
            ProductView::create([
                'product_id' => $product->id,
                'user_id' => Auth::check() ? Auth::id() : null,
                'ip_address' => $request->ip(),
                'viewed_at' => now(),
            ]);
            session()->put($viewedKey, now());
        }

        // 2. Fetch variants
        $variants = ProductVariant::where('product_id', $product->id)
            ->where('status', 'active')
            ->with(['color', 'size'])
            ->get();

        // Unique colors and sizes from variants
        $colors = $variants->pluck('color')->unique('id')->filter();
        $sizes = $variants->pluck('size')->unique('id')->filter();

        // 3. Fetch reviews with filters
        $reviewsQuery = ProductReview::where('product_id', $product->id)
            ->where('status', 'approved')
            ->with('user');

        // Review star filter
        if ($request->filled('rating')) {
            $reviewsQuery->where('rating', $request->rating);
        }

        // Review photo filter
        if ($request->boolean('has_image')) {
            $reviewsQuery->whereNotNull('image');
        }

        // Sort reviews: latest vs top voted
        $reviewSort = $request->get('review_sort', 'latest');
        if ($reviewSort === 'helpful') {
            $reviewsQuery->withCount('votes')
                ->orderByDesc('votes_count');
        } else {
            $reviewsQuery->latest();
        }

        $reviews = $reviewsQuery->paginate(5)->withQueryString();

        // Optimized Review Stats: 1 single SQL query instead of 7 queries
        $reviewStats = ProductReview::where('product_id', $product->id)
            ->where('status', 'approved')
            ->selectRaw('
                COUNT(*) as total_reviews,
                COALESCE(AVG(rating), 0) as avg_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as star_5,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as star_4,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as star_3,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as star_2,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as star_1
            ')->first();

        $totalReviews = (int) ($reviewStats->total_reviews ?? 0);
        $avgRating = (float) ($reviewStats->avg_rating ?? 0.0);
        $starDistribution = [
            5 => (int) ($reviewStats->star_5 ?? 0),
            4 => (int) ($reviewStats->star_4 ?? 0),
            3 => (int) ($reviewStats->star_3 ?? 0),
            2 => (int) ($reviewStats->star_2 ?? 0),
            1 => (int) ($reviewStats->star_1 ?? 0),
        ];

        $relatedProducts = Product::where('status', 'active')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['brandRelation', 'category'])
            ->limit(4)
            ->get();

        // Check if current user has purchased this product (required to review)
        $hasPurchased = false;
        if (Auth::check()) {
            $hasPurchased = OrderItem::where('product_id', $product->id)
                ->whereHas('order', function ($q) {
                    $q->where('user_id', Auth::id())
                      ->where('order_status', 'completed');
                })->exists();
        }

        // Get user settings for theme
        $theme = $request->cookie('theme', 'light');
        if (Auth::check() && Auth::user()->settings) {
            $theme = Auth::user()->settings->theme;
        }

        return view('products.show', compact(
            'product', 
            'variants', 
            'colors', 
            'sizes', 
            'reviews', 
            'totalReviews', 
            'avgRating', 
            'starDistribution', 
            'relatedProducts', 
            'hasPurchased',
            'theme'
        ));
    }

    /**
     * API to get variant price and stock for selected color & size.
     */
    public function getVariantInfo(Request $request, $productId)
    {
        $colorId = $request->color_id;
        $sizeId = $request->size_id;

        $query = ProductVariant::where('product_id', $productId)
            ->where('status', 'active');

        if ($colorId) {
            $query->where('color_id', $colorId);
        }
        if ($sizeId) {
            $query->where('size_id', $sizeId);
        }

        $variant = $query->first();

        if ($variant) {
            $product = Product::findOrFail($productId);
            $price = $variant->price !== null ? $variant->price : $product->price;
            $salePrice = $variant->sale_price !== null ? $variant->sale_price : $product->sale_price;

            return response()->json([
                'success' => true,
                'variant_id' => $variant->id,
                'sku' => $variant->sku,
                'price' => number_format($price, 0, ',', '.') . ' ₫',
                'sale_price' => $salePrice ? number_format($salePrice, 0, ',', '.') . ' ₫' : null,
                'has_sale' => $salePrice !== null && $salePrice < $price,
                'stock_quantity' => $variant->stock_quantity,
                'thumbnail' => $variant->thumbnail ? asset('storage/' . $variant->thumbnail) : null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy biến thể phù hợp.'
        ]);
    }

    /**
     * Store a product review.
     */
    public function storeReview(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // max 2MB
        ], [
            'rating.required' => 'Vui lòng đánh giá số sao.',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($productId);

        // Verify purchase
        $hasPurchased = OrderItem::where('product_id', $productId)
            ->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('order_status', 'completed');
            })->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'Bạn chỉ có thể đánh giá sản phẩm sau khi đã mua và hoàn thành đơn hàng.');
        }

        // Get one order id for the product review record
        $orderItem = OrderItem::where('product_id', $productId)
            ->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('order_status', 'completed');
            })->first();

        $imgPath = null;
        if ($request->hasFile('image')) {
            $imgPath = $request->file('image')->store('reviews', 'public');
        }

        ProductReview::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_id' => $orderItem ? $orderItem->order_id : null,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'image' => $imgPath,
            'status' => 'approved', // Auto approve reviews for simple setup
            'is_featured' => false,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá cho sản phẩm!');
    }

    /**
     * Upvote/Helpful vote on a review.
     */
    public function voteReview($reviewId)
    {
        $user = Auth::user();
        $review = ProductReview::findOrFail($reviewId);

        $existing = ReviewVote::where('user_id', $user->id)
            ->where('review_id', $reviewId)
            ->first();

        if ($existing) {
            $existing->delete();
            $msg = 'Đã hủy bình chọn hữu ích.';
            $voted = false;
        } else {
            ReviewVote::create([
                'user_id' => $user->id,
                'review_id' => $reviewId,
                'vote_type' => 'upvote',
            ]);
            $msg = 'Đã bình chọn đánh giá này hữu ích.';
            $voted = true;
        }

        $votesCount = ReviewVote::where('review_id', $reviewId)->count();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'voted' => $voted,
                'votes_count' => $votesCount,
                'message' => $msg
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Toggle wishlist (save/remove).
     */
    public function toggleWishlist(Request $request, $productId)
    {
        $user = Auth::user();
        $wishlist = \App\Models\Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $msg = 'Đã xóa sản phẩm khỏi danh sách quan tâm.';
            $saved = false;
        } else {
            \App\Models\Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
            $msg = 'Đã lưu sản phẩm vào danh sách quan tâm.';
            $saved = true;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'saved' => $saved,
                'message' => $msg
            ]);
        }

        return back()->with('success', $msg);
    }

    public function getQuickView($id)
    {
        $product = Product::with(['category', 'brandRelation', 'warranties', 'images'])->findOrFail($id);

        $variants = ProductVariant::where('product_id', $product->id)
            ->where('status', 'active')
            ->with(['color', 'size'])
            ->get();

        $colors = $variants->pluck('color')->unique('id')->filter()->values();
        $sizes = $variants->pluck('size')->unique('id')->filter()->values();

        $avgRating = ProductReview::where('product_id', $product->id)->where('status', 'approved')->avg('rating') ?? 5.0;
        $totalReviews = ProductReview::where('product_id', $product->id)->where('status', 'approved')->count();

        return response()->json([
            'success' => true,
            'id' => $product->id,
            'code' => $product->code,
            'name' => $product->name,
            'slug' => $product->slug,
            'brand' => $product->brandRelation ? $product->brandRelation->name : ($product->brand ?? 'GiaDụngPro'),
            'price' => number_format($product->price, 0, ',', '.') . ' ₫',
            'sale_price' => ($product->sale_price !== null && $product->sale_price < $product->price) ? number_format($product->sale_price, 0, ',', '.') . ' ₫' : null,
            'discount_percent' => ($product->sale_price && $product->sale_price < $product->price) ? round((($product->price - $product->sale_price) / $product->price) * 100) : 0,
            'thumbnail' => asset('storage/' . $product->thumbnail),
            'short_description' => $product->short_description ?? 'Sản phẩm gia dụng cao cấp chính hãng.',
            'description' => $product->description ?? 'Thông tin chi tiết sản phẩm đang được cập nhật.',
            'unit' => $product->unit,
            'is_domestic' => $product->is_domestic,
            'stock_quantity' => $product->stock_quantity,
            'in_stock' => $product->stock_quantity > 0,
            'avg_rating' => number_format($avgRating, 1),
            'total_reviews' => $totalReviews,
            'colors' => $colors,
            'sizes' => $sizes,
            'warranties' => $product->warranties,
            'detail_url' => route('products.show', $product->slug)
        ]);
    }
}
