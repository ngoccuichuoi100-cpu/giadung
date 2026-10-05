<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalOrders = Order::count();
        $pendingOrdersCount = Order::where('order_status', 'pending')->count();

        // Standardized revenue definition: completed orders OR paid orders, excluding cancelled and refunded
        $revenueScope = function ($query) {
            $query->where(function ($q) {
                $q->where('order_status', 'completed')
                  ->orWhere('payment_status', 'paid');
            })
            ->where('order_status', '!=', 'cancelled')
            ->where('payment_status', '!=', 'refunded');
        };

        $totalRevenue = Order::where($revenueScope)->sum('total_amount');

        $latestOrders = Order::with('user')->latest()->limit(6)->get();
        $lowStockProducts = Product::with('category')->where('stock_quantity', '<=', 5)
            ->orderBy('stock_quantity', 'asc')
            ->limit(6)
            ->get();

        // Monthly revenue calculations for selected year
        $year = $request->get('year', date('Y'));
        
        $monthlyRevenue = [];
        $monthlyOrdersCount = [];
        $monthlyAvgOrderValue = [];
        
        for ($m = 1; $m <= 12; $m++) {
            $monthOrders = Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->where($revenueScope)
                ->get();

            $revenue = $monthOrders->sum('total_amount');
            $count = $monthOrders->count();

            $monthlyRevenue[$m] = $revenue;
            $monthlyOrdersCount[$m] = $count;
            $monthlyAvgOrderValue[$m] = $count > 0 ? $revenue / $count : 0;
        }

        $totalVouchers = \App\Models\Voucher::count();
        $activeVouchers = \App\Models\Voucher::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalCustomers',
            'totalOrders',
            'pendingOrdersCount',
            'totalRevenue',
            'totalVouchers',
            'activeVouchers',
            'latestOrders',
            'lowStockProducts',
            'year',
            'monthlyRevenue',
            'monthlyOrdersCount',
            'monthlyAvgOrderValue'
        ));
    }
}
