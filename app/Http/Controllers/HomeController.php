<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\User;
use App\Services\ProductPopularityService;
use App\Services\ProductRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index(Request $request, ProductRecommendationService $recommendationService)
    {
        $categories = Category::where('status', 'active')->get();

        $featuredProducts = Product::where('status', 'active')
            ->where('is_featured', true)
            ->latest()
            ->limit(8)
            ->get();

        $newProducts = Product::where('status', 'active')
            ->latest()
            ->limit(8)
            ->get();

        $saleProducts = Product::where('status', 'active')
            ->whereNotNull('sale_price')
            ->whereColumn('sale_price', '<', 'price')
            ->latest()
            ->limit(8)
            ->get();

        // 1. Brands with product count
        $brands = Brand::where('status', 'active')
            ->withCount(['products' => function($q) {
                $q->where('status', 'active');
            }])
            ->get();

        // 2. Brand purchase ranking
        $range = $request->get('brand_range', 'all');
        $rankQuery = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->where('orders.order_status', 'completed')
            ->select('brands.id', 'brands.name', 'brands.logo', DB::raw('SUM(order_items.quantity) as sold_count'));

        if ($range === 'week') {
            $rankQuery->where('orders.created_at', '>=', now()->subWeek());
        } elseif ($range === 'month') {
            $rankQuery->where('orders.created_at', '>=', now()->subMonth());
        } elseif ($rankQuery === 'year') {
            $rankQuery->where('orders.created_at', '>=', now()->subYear());
        }

        // Get admin configured display limit, default 5
        $limit = 5;
        if (Storage::exists('settings.json')) {
            $data = json_decode(Storage::get('settings.json'), true);
            $limit = $data['brand_rank_limit'] ?? 5;
        }

        $brandRankings = $rankQuery->groupBy('brands.id', 'brands.name', 'brands.logo')
            ->orderByDesc('sold_count')
            ->limit($limit)
            ->get();

        // 3. Platform reviews statistics
        $reviewsQuery = ProductReview::where('status', 'approved');
        $totalReviews = $reviewsQuery->count();
        $avgRating = $reviewsQuery->avg('rating') ?? 0.0;
        
        $ratingDistribution = [];
        for ($star = 5; $star >= 1; $star--) {
            $ratingDistribution[$star] = (clone $reviewsQuery)->where('rating', $star)->count();
        }

        // Feature reviews (recent 3 approved high-rating reviews)
        $featuredReviews = ProductReview::where('status', 'approved')
            ->where('rating', '>=', 4)
            ->with(['user', 'product'])
            ->latest()
            ->limit(3)
            ->get();

        // 4. Popular products by custom weights service
        $popularityService = resolve(ProductPopularityService::class);
        $popularProducts = $popularityService->getPopularProducts(8);

        // 5. Non-sensitive public statistics
        $publicStats = [
            'products_count' => Product::where('status', 'active')->count(),
            'brands_count' => Brand::where('status', 'active')->count(),
            'orders_served' => Order::where('order_status', 'completed')->count(),
        ];

        // 6. Personalized recommendations based on search history
        $searchRecommendation = $recommendationService->getRecommendations($request, 4);

        return view('home', compact(
            'categories', 
            'featuredProducts', 
            'newProducts', 
            'saleProducts',
            'brands',
            'brandRankings',
            'totalReviews',
            'avgRating',
            'ratingDistribution',
            'featuredReviews',
            'popularProducts',
            'publicStats',
            'searchRecommendation'
        ));
    }

    /**
     * Preview custom homepage design without affecting the main page.
     */
    public function preview(Request $request, ProductRecommendationService $recommendationService)
    {
        $view = $this->index($request, $recommendationService);
        return view('home-preview', $view->getData());
    }

    /**
     * Display the 'About Us' page.
     */
    public function about()
    {
        // Admin editable fields loaded from settings.json if exists
        $aboutData = [
            'intro' => 'GiaDụngPro tự hào là nhà bán lẻ các thiết bị nhà bếp, nồi cơm, máy xay chính hãng cao cấp hàng đầu Việt Nam.',
            'history' => 'Được thành lập từ năm 2018, chúng tôi đã phục vụ hơn 100,000 gia đình Việt.',
            'vision' => 'Trở thành điểm đến tin cậy nhất cho các thiết bị thông minh tiện ích nâng tầm cuộc sống.',
            'partnership' => 'Chúng tôi hợp tác với các thương hiệu lớn như Philips, Cuckoo, Panasonic, Tefal.',
            'addresses' => [
                'Cửa hàng Hà Nội: 123 Đường Laragon, Quận Cầu Giấy',
                'Cửa hàng TP.HCM: 456 Đường PHP, Quận 1',
            ],
            'achievements' => [
                'Cup Thương Hiệu Việt Nam Tin Dùng 2024',
                'Top 10 Nhà phân phối đồ gia dụng xuất sắc nhất',
            ],
        ];

        if (Storage::exists('settings.json')) {
            $settings = json_decode(Storage::get('settings.json'), true);
            if (isset($settings['about'])) {
                $aboutData = array_merge($aboutData, $settings['about']);
            }
        }

        // Get brand partners from brand partnerships table
        $partners = \App\Models\BrandPartnership::where('status', 'active')->get();

        return view('about', compact('aboutData', 'partners'));
    }
}
