@extends('layouts.admin')

@section('title', 'Admin Dashboard - GiaDụngPro')

@section('content')
@php
    $user = auth()->user();
    $roleTitle = 'Quản trị viên';
    if ($user) {
        if ($user->isAdmin()) $roleTitle = 'Quản trị viên hệ thống';
        elseif ($user->isWarehouse()) $roleTitle = 'Nhân viên Quản lý Kho';
        elseif ($user->isSales()) $roleTitle = 'Nhân viên Kinh doanh';
        else $roleTitle = 'Nhân viên';
    }

    $yearRevenue = array_sum($monthlyRevenue);
    $yearOrders = array_sum($monthlyOrdersCount);
    $bestMonth = 1;
    $maxRevenue = 0;
    foreach($monthlyRevenue as $m => $val) {
        if ($val > $maxRevenue) {
            $maxRevenue = $val;
            $bestMonth = $m;
        }
    }
    $avgOrderValue = $yearOrders > 0 ? $yearRevenue / $yearOrders : 0;
    $avgMonthlyRevenue = $yearRevenue / 12;
@endphp

<div class="mt-6 space-y-8 text-left">
    <!-- Welcome & Quick Actions Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-white border border-slate-200/90 p-6 md:p-8 shadow-xs">
        <!-- Ambient Decorative Background Elements with Candy Blue -->
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-gradient-to-br from-[#B2D5E5]/30 to-sky-100/30 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-gradient-to-tr from-slate-100/60 to-[#B2D5E5]/20 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex items-center justify-between gap-4">
            <div class="max-w-xl">
                <h1 class="text-xl md:text-2xl font-black text-slate-900 tracking-tight">
                    Xin chào, {{ $user->name }}! 
                </h1>
                <p class="text-xs md:text-sm text-slate-500 font-medium mt-1">
                    @if($user->isAdmin())
                        Theo dõi tổng quan doanh thu, đơn hàng & tồn kho hệ sinh thái GiaDụngPro hôm nay.
                    @elseif($user->isWarehouse())
                        Cập nhật trạng thái nhập xuất hàng hóa và các sản phẩm sắp chạm ngưỡng tồn kho tối thiểu.
                    @elseif($user->isSales())
                        Kiểm tra đơn hàng chờ xác nhận, quản lý thông tin khách hàng và lịch trực bán hàng.
                    @endif
                </p>
            </div>

            <!-- Logo & Gia Dụng mảng phải -->
            <div class="shrink-0 flex flex-col items-center justify-center select-none group mr-8 md:mr-16 lg:mr-24">
                <img src="{{ asset('images/logo-transparent.png') }}" alt="Gia Dụng" class="h-14 md:h-16 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
                <span class="text-base md:text-lg font-black text-slate-900 tracking-wide mt-1.5">Gia Dụng</span>
            </div>
        </div>
    </div>

    <!-- 6 Overview Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <!-- 1. Sản phẩm -->
        <a href="{{ route('admin.products.index') }}" class="group bg-white border border-slate-200/90 hover:border-[#B2D5E5] rounded-2xl p-4 md:p-5 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-100 group-hover:scale-105 transition-transform flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <span class="text-[11px] font-bold text-slate-400 group-hover:text-sky-600 flex items-center gap-0.5 whitespace-nowrap transition-colors">
                    Kho
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Sản phẩm</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight mt-0.5 block">{{ number_format($totalProducts) }}</span>
            </div>
        </a>

        <!-- 2. Danh mục -->
        <a href="{{ route('admin.categories.index') }}" class="group bg-white border border-slate-200/90 hover:border-indigo-300 rounded-2xl p-4 md:p-5 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 group-hover:scale-105 transition-transform flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <span class="text-[11px] font-bold text-slate-400 group-hover:text-indigo-600 flex items-center gap-0.5 whitespace-nowrap transition-colors">
                    Phân loại
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Danh mục</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight mt-0.5 block">{{ number_format($totalCategories) }}</span>
            </div>
        </a>

        <!-- 3. Khách hàng -->
        <a href="{{ route('admin.customers.index') }}" class="group bg-white border border-slate-200/90 hover:border-emerald-300 rounded-2xl p-4 md:p-5 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 group-hover:scale-105 transition-transform flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <span class="text-[11px] font-bold text-slate-400 group-hover:text-emerald-600 flex items-center gap-0.5 whitespace-nowrap transition-colors">
                    Thành viên
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Khách hàng</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight mt-0.5 block">{{ number_format($totalCustomers) }}</span>
            </div>
        </a>

        <!-- 4. Đơn hàng -->
        <a href="{{ route('admin.orders.index') }}" class="group bg-white border border-slate-200/90 hover:border-purple-300 rounded-2xl p-4 md:p-5 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100 group-hover:scale-105 transition-transform flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <span class="text-[11px] font-bold text-slate-400 group-hover:text-purple-600 flex items-center gap-0.5 whitespace-nowrap transition-colors">
                    Tất cả
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Đơn hàng</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight mt-0.5 block">{{ number_format($totalOrders) }}</span>
            </div>
        </a>

        <!-- 5. Chờ duyệt -->
        <a href="{{ route('admin.orders.index') }}" class="group bg-white border {{ $pendingOrdersCount > 0 ? 'border-amber-300 bg-gradient-to-b from-amber-50/20 to-white' : 'border-slate-200/90' }} hover:border-amber-400 rounded-2xl p-4 md:p-5 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 group-hover:scale-105 transition-transform relative flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    @if($pendingOrdersCount > 0)
                        <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-amber-500 rounded-full animate-ping"></span>
                    @endif
                </div>
                <span class="text-[11px] font-bold {{ $pendingOrdersCount > 0 ? 'text-amber-600 group-hover:text-amber-700' : 'text-slate-400 group-hover:text-amber-600' }} flex items-center gap-0.5 whitespace-nowrap transition-colors">
                    {{ $pendingOrdersCount > 0 ? 'Cần xử lý' : 'Đã duyệt' }}
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Chờ duyệt</span>
                <span class="text-2xl font-black {{ $pendingOrdersCount > 0 ? 'text-amber-600' : 'text-slate-900' }} tracking-tight mt-0.5 block">{{ number_format($pendingOrdersCount) }}</span>
            </div>
        </a>

        <!-- 6. Doanh thu thực thu -->
        <div class="bg-gradient-to-br from-[#B2D5E5]/30 via-white to-white border border-[#B2D5E5] rounded-2xl p-4 md:p-5 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="w-10 h-10 rounded-xl bg-orange-500 text-slate-900 flex items-center justify-center font-bold shadow-xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-[10px] font-bold text-sky-800 uppercase tracking-wider bg-sky-100/70 px-2 py-0.5 rounded-md">
                    Thực thu
                </span>
            </div>
            <div class="mt-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Tổng doanh thu</span>
                <span class="text-lg font-black text-slate-900 tracking-tight mt-0.5 truncate block" title="{{ number_format($totalRevenue, 0, ',', '.') }} ₫">
                    {{ number_format($totalRevenue, 0, ',', '.') }} ₫
                </span>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue & Analytics Section -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 md:p-8 shadow-xs space-y-6">
        <!-- Section Header with Year Picker & Tabs -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-5">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-[#B2D5E5]/40 text-slate-800 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h2 class="font-extrabold text-slate-900 text-lg tracking-tight">Thống kê doanh thu 12 tháng (Năm {{ $year }})</h2>
                </div>
                <p class="text-xs text-slate-400 mt-1">Doanh số thực tế dựa trên đơn hoàn thành hoặc đã thanh toán (không tính đơn hủy/hoàn tiền)</p>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <!-- View Toggle Buttons -->
                <div class="inline-flex p-1 bg-slate-100 rounded-xl text-xs font-bold text-slate-600">
                    <button type="button" id="btnViewChart" onclick="switchAnalyticsView('chart')" class="px-3 py-1.5 rounded-lg bg-white text-slate-900 shadow-xs transition-all flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Biểu đồ
                    </button>
                    <button type="button" id="btnViewTable" onclick="switchAnalyticsView('table')" class="px-3 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition-all flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        Bảng số liệu
                    </button>
                </div>

                <!-- Year Select Form -->
                <form action="{{ route('admin.dashboard') }}" method="GET" class="relative">
                    <select name="year" onchange="this.form.submit()" class="bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl pl-3 pr-8 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#B2D5E5] transition-all cursor-pointer">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>
        </div>

        <!-- 4 Mini KPI Highlights for Selected Year -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-150">
            <div class="space-y-0.5">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Tổng doanh thu năm</span>
                <span class="text-base md:text-lg font-black text-slate-900 block truncate">{{ number_format($yearRevenue, 0, ',', '.') }} ₫</span>
            </div>
            <div class="space-y-0.5">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Tổng đơn phát sinh</span>
                <span class="text-base md:text-lg font-black text-slate-900 block">{{ number_format($yearOrders) }} <span class="text-xs font-semibold text-slate-500">đơn</span></span>
            </div>
            <div class="space-y-0.5">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Tháng cao điểm nhất</span>
                <span class="text-base md:text-lg font-black text-orange-600 block">Tháng {{ $bestMonth }} <span class="text-xs font-bold text-slate-500">({{ number_format($maxRevenue, 0, ',', '.') }} ₫)</span></span>
            </div>
            <div class="space-y-0.5">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Giá trị đơn TB (AOV)</span>
                <span class="text-base md:text-lg font-black text-slate-900 block truncate">{{ number_format($avgOrderValue, 0, ',', '.') }} ₫</span>
            </div>
        </div>

        <!-- 1. Interactive Chart View (Default) -->
        <div id="analyticsChartView" class="space-y-4">
            <div class="relative w-full h-72">
                <canvas id="revenueChartCanvas"></canvas>
            </div>
        </div>

        <!-- 2. Detailed Data Table View (Toggleable) -->
        <div id="analyticsTableView" class="hidden overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left border-collapse text-xs text-slate-700">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="p-3.5">Tháng</th>
                        <th class="p-3.5 text-right">Doanh thu thực tế</th>
                        <th class="p-3.5 text-center">Số đơn hàng</th>
                        <th class="p-3.5 text-right">Giá trị đơn trung bình</th>
                        <th class="p-3.5 text-center">Tỷ trọng năm</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $rev = $monthlyRevenue[$m];
                            $ratio = $yearRevenue > 0 ? round(($rev / $yearRevenue) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $m == $bestMonth && $rev > 0 ? 'bg-sky-50/30' : '' }}">
                            <td class="p-3.5 font-bold text-slate-900 flex items-center gap-2">
                                Tháng {{ $m }}
                                @if($m == $bestMonth && $rev > 0)
                                    <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-full bg-[#B2D5E5] text-slate-900">Cao nhất</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right font-black text-slate-900">
                                {{ number_format($rev, 0, ',', '.') }} ₫
                            </td>
                            <td class="p-3.5 text-center font-bold text-slate-700">
                                {{ $monthlyOrdersCount[$m] }} đơn
                            </td>
                            <td class="p-3.5 text-right text-slate-500 font-semibold">
                                {{ number_format($monthlyAvgOrderValue[$m], 0, ',', '.') }} ₫
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <div class="w-16 h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-orange-400 rounded-full" style="width: {{ $ratio }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-500 w-8 text-right">{{ $ratio }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom Section: Latest Orders (2 cols) & Low Stock Warning (1 col) -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left: Latest Orders -->
        <div class="xl:col-span-2 bg-white border border-slate-200/90 rounded-3xl p-6 md:p-8 shadow-xs space-y-5">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <div>
                        <h2 class="font-extrabold text-slate-900 text-base tracking-tight">Đơn hàng mới nhất</h2>
                        <p class="text-[11px] text-slate-400">Giao dịch phát sinh gần đây • Rê chuột vào icon để xem chi tiết trạng thái</p>
                    </div>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-slate-700 hover:text-slate-950 flex items-center gap-1 hover:underline">
                    Xem tất cả ({{ $totalOrders }})
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left border-collapse text-xs text-slate-700">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="p-3.5 whitespace-nowrap">Mã đơn</th>
                            <th class="p-3.5 whitespace-nowrap">Khách hàng</th>
                            <th class="p-3.5 text-right whitespace-nowrap">Tổng tiền</th>
                            <th class="p-3.5 text-center whitespace-nowrap w-24">Thanh toán</th>
                            <th class="p-3.5 text-center whitespace-nowrap w-24">Trạng thái</th>
                            <th class="p-3.5 text-center whitespace-nowrap w-20">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($latestOrders as $order)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-3.5 whitespace-nowrap">
                                    <span class="font-mono font-black text-slate-900 bg-slate-100 px-2 py-1 rounded-md text-[11px]">
                                        #{{ $order->order_code }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block mt-1">
                                        {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '' }}
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-900 truncate max-w-[150px] sm:max-w-[200px]" title="{{ $order->receiver_name }}">{{ $order->receiver_name }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium">{{ $order->receiver_phone }}</div>
                                </td>
                                <td class="p-3.5 text-right font-black text-slate-900 text-sm whitespace-nowrap">
                                    {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                                </td>
                                <td class="p-3.5 text-center whitespace-nowrap">
                                    @if($order->payment_status === 'paid')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đã thanh toán">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đã thanh toán</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @elseif($order->payment_status === 'failed')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-rose-100 text-rose-700 border border-rose-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Thanh toán thất bại">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Thất bại</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @elseif($order->payment_status === 'refunded')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-purple-100 text-purple-700 border border-purple-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đã hoàn tiền">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đã hoàn tiền</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-amber-100 text-amber-700 border border-amber-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Chưa thanh toán">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Chưa thanh toán</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center whitespace-nowrap">
                                    @if($order->order_status === 'completed')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Hoàn thành">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l5-5z" clip-rule="evenodd"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Hoàn thành</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @elseif($order->order_status === 'cancelled')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-rose-100 text-rose-700 border border-rose-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đã hủy">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đã hủy</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @elseif($order->order_status === 'pending')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-amber-100 text-amber-700 border border-amber-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help animate-pulse" title="Chờ duyệt">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Chờ duyệt</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @elseif($order->order_status === 'confirmed')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-sky-100 text-sky-700 border border-sky-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đã xác nhận">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đã xác nhận</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @elseif($order->order_status === 'processing')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 border border-indigo-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đang chuẩn bị">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đang chuẩn bị</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @elseif($order->order_status === 'shipping')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 border border-blue-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đang giao hàng">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đang giao hàng</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 border border-slate-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="{{ $order->order_status }}">
                                                <span class="text-[10px] font-bold uppercase">{{ substr($order->order_status, 0, 1) }}</span>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">{{ $order->order_status }}</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-lg text-xs font-bold border border-slate-200 transition-all">
                                        Xem
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 italic">
                                    Chưa có đơn hàng nào được ghi nhận trong hệ thống.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Low Stock Alert Card -->
        <div class="bg-white border border-slate-200/90 rounded-3xl p-6 md:p-8 shadow-xs space-y-5">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h2 class="font-extrabold text-slate-900 text-base tracking-tight flex items-center gap-2">
                            Sắp hết hàng
                            @if(count($lowStockProducts) > 0)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-100 text-red-700">
                                    {{ count($lowStockProducts) }}
                                </span>
                            @endif
                        </h2>
                        <p class="text-[11px] text-slate-400">Các mặt hàng dưới ngưỡng an toàn (≤ 5)</p>
                    </div>
                </div>
                <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-slate-700 hover:text-slate-950 flex items-center gap-1 hover:underline">
                    Quản lý kho
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="space-y-3">
                @forelse($lowStockProducts as $prod)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/80 hover:bg-slate-100/80 border border-slate-150 transition-all gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 bg-white rounded-xl overflow-hidden border border-slate-200 flex-shrink-0 flex items-center justify-center">
                                @if($prod->thumbnail)
                                    <img src="{{ $prod->thumbnail_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-slate-900 text-xs truncate max-w-[150px] sm:max-w-[190px]" title="{{ $prod->name }}">
                                    {{ $prod->name }}
                                </h4>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] text-slate-400 font-mono">#{{ $prod->code }}</span>
                                    @if($prod->category)
                                        <span class="text-[9px] px-1.5 py-0.2 rounded bg-slate-200 text-slate-600 font-semibold">{{ $prod->category->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="text-right flex-shrink-0 flex flex-col items-end gap-1">
                            <span class="px-2.5 py-1 text-xs rounded-xl font-black {{ $prod->stock_quantity <= 1 ? 'bg-red-500 text-white' : 'bg-red-100 text-red-700' }}">
                                Còn: {{ $prod->stock_quantity }} {{ $prod->unit ?? 'cái' }}
                            </span>
                            <a href="{{ route('admin.products.edit', $prod->id) }}" class="text-[10px] text-slate-400 hover:text-slate-900 font-bold hover:underline">
                                Nhập thêm →
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center bg-emerald-50/50 rounded-2xl border border-emerald-100">
                        <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-xs font-bold text-emerald-800">Kho hàng an toàn!</p>
                        <p class="text-[11px] text-emerald-600 mt-0.5">Không có sản phẩm nào chạm ngưỡng tồn kho tối thiểu.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Dashboard Analytics & Chart Script -->
    <script>
        // Tab switcher between Chart and Table (exposed globally on window)
        window.switchAnalyticsView = function(mode) {
            const chartView = document.getElementById('analyticsChartView');
            const tableView = document.getElementById('analyticsTableView');
            const btnChart = document.getElementById('btnViewChart');
            const btnTable = document.getElementById('btnViewTable');

            if (!chartView || !tableView) return;

            if (mode === 'chart') {
                chartView.classList.remove('hidden');
                tableView.classList.add('hidden');
                if (btnChart) {
                    btnChart.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
                    btnChart.classList.remove('text-slate-500');
                }
                if (btnTable) {
                    btnTable.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
                    btnTable.classList.add('text-slate-500');
                }
            } else {
                chartView.classList.add('hidden');
                tableView.classList.remove('hidden');
                if (btnTable) {
                    btnTable.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
                    btnTable.classList.remove('text-slate-500');
                }
                if (btnChart) {
                    btnChart.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
                    btnChart.classList.add('text-slate-500');
                }
            }
        };

        // Initialize Chart.js with Candy Blue styling (Supports both direct load and SPA transition)
        window.initRevenueChart = function() {
            const ctx = document.getElementById('revenueChartCanvas');
            if (!ctx) return;

            // Destroy previous chart instance if exists to avoid canvas reuse error
            if (window.revenueChartInstance) {
                try {
                    window.revenueChartInstance.destroy();
                } catch(e) {}
            }

            if (typeof Chart === 'undefined') {
                setTimeout(window.initRevenueChart, 80);
                return;
            }

            const months = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
            const revenueData = @json(array_values($monthlyRevenue));
            const ordersData = @json(array_values($monthlyOrdersCount));
            const formatVND = (value) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);

            window.revenueChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Doanh thu (VNĐ)',
                            data: revenueData,
                            backgroundColor: '#B2D5E5',
                            hoverBackgroundColor: '#97C3D7',
                            borderColor: '#7CB1CA',
                            borderWidth: 1.5,
                            borderRadius: 8,
                            borderSkipped: false,
                            maxBarThickness: 46,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0F172A',
                            titleColor: '#FFFFFF',
                            bodyColor: '#E2E8F0',
                            titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 12, weight: 'bold' },
                            bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                            padding: 12,
                            cornerRadius: 10,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    const orders = ordersData[context.dataIndex] || 0;
                                    return [
                                        'Doanh thu: ' + formatVND(context.raw),
                                        'Số đơn hàng: ' + orders + ' đơn'
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: 'bold' },
                                color: '#94A3B8'
                            }
                        },
                        y: {
                            type: 'linear',
                            beginAtZero: true,
                            grid: { color: 'rgba(226, 232, 240, 0.7)' },
                            ticks: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 10 },
                                color: '#94A3B8',
                                callback: function (val) {
                                    if (val >= 1000000) return (val / 1000000).toFixed(1) + ' tr';
                                    if (val >= 1000) return (val / 1000).toFixed(0) + ' k';
                                    return val + ' ₫';
                                }
                            }
                        }
                    }
                }
            });
        };

        // Trigger on DOM ready, immediate or SPA page transition
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', window.initRevenueChart);
        } else {
            window.initRevenueChart();
        }
        document.addEventListener('admin:page-loaded', window.initRevenueChart);
    </script>
</div>
@endsection
