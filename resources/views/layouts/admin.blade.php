@php
    $user = auth()->user();
    $roleName = 'Quản trị viên';
    if ($user) {
        if ($user->isAdmin()) {
            $roleName = 'Quản trị viên';
        } elseif ($user->isWarehouse()) {
            $roleName = 'Nhân viên Kho';
        } elseif ($user->isSales()) {
            $roleName = 'Nhân viên Bán hàng';
        } else {
            $roleName = 'Nhân viên';
        }
    }
@endphp
<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - GiaDụngPro')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Force light mode
        document.documentElement.classList.remove('dark');
        document.cookie = 'theme=light; path=/; max-age=31536000';
    </script>
</head>
<body class="h-full flex bg-slate-100 text-slate-800">

    <!-- Top Loading Progress Bar (YouTube/GitHub style) -->
    <div id="adminTopProgressBar" class="fixed top-0 left-0 h-1 bg-gradient-to-r from-orange-500 via-amber-400 to-sky-500 z-[9999] pointer-events-none transition-all duration-300 w-0 opacity-0 shadow-[0_0_10px_rgba(249,115,22,0.8)]"></div>

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-100 text-slate-700 flex flex-col flex-shrink-0 border-r border-slate-200 shadow-xs">
        <div class="h-16 flex items-center px-6 bg-white border-b border-slate-200">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo-transparent.png') }}" alt="GiaDụng" class="h-8 w-auto object-contain">
                <div class="flex flex-col">
                    <span class="font-extrabold text-lg text-slate-900 tracking-tight leading-none">GiaDụng</span>
                    <span class="text-[10px] text-sky-600 font-bold uppercase tracking-wider mt-0.5">{{ $roleName }}</span>
                </div>
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <!-- Dashboard (Tất cả vai trò đều xem được) -->
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Kho & Sản phẩm: Admin hoặc Kho -->
            @if($user && ($user->isAdmin() || $user->isWarehouse()))
                <a href="{{ route('admin.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span>Quản lý danh mục</span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>Quản lý sản phẩm</span>
                </a>
            @endif

            <!-- Đơn hàng & Khách hàng: Admin hoặc Bán hàng -->
            @if($user && ($user->isAdmin() || $user->isSales()))
                <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Quản lý đơn hàng</span>
                </a>
                <a href="{{ route('admin.customers.index') }}" class="admin-nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Quản lý khách hàng</span>
                </a>
                <a href="{{ route('admin.vouchers.index') }}" class="admin-nav-link flex items-center justify-between {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                        </svg>
                        <span class="truncate">Quản lý ưu đãi</span>
                    </div>
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 flex-shrink-0">Đang nâng cấp</span>
                </a>
                <a href="{{ route('admin.news.index') }}" class="admin-nav-link flex items-center justify-between {{ request()->routeIs('admin.news.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                        <span class="truncate">Quản lý tin tức</span>
                    </div>
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 flex-shrink-0">Đang nâng cấp</span>
                </a>
            @endif

            <!-- Quản lý nhân sự: Chỉ Admin -->
            @if($user && $user->isAdmin())
                <a href="{{ route('admin.employees.index') }}" class="admin-nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Quản lý nhân sự</span>
                </a>
            @endif

            <!-- Lịch trực bán hàng: Admin hoặc Bán hàng -->
            @if($user && ($user->isAdmin() || $user->isSales()))
                <a href="{{ route('admin.sales-schedules.index') }}" class="admin-nav-link {{ request()->routeIs('admin.sales-schedules.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Lịch trực bán hàng</span>
                </a>
            @endif

            <div class="border-t border-slate-200 my-4"></div>
            <a href="{{ route('home') }}" class="admin-nav-link">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Về trang chủ</span>
            </a>
        </nav>

        <!-- Footer / Logout -->
        <div class="p-4 border-t border-slate-200 bg-white">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 active:bg-red-200 border border-red-200 rounded-xl transition-all shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Thoát</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden relative">
        <!-- Top Header -->
        <header class="h-16 bg-white border-b border-slate-200/90 flex justify-between items-center px-6 md:px-8 shadow-xs flex-shrink-0">
            <!-- Left: Breadcrumb & Title -->
            <div id="adminHeaderLeftArea" class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-400">
                        <span>GiaDụngPro</span>
                        <span>/</span>
                        <span class="text-slate-600 font-bold">Quản trị</span>
                    </div>
                    <div class="flex items-center gap-2 mt-0.5">
                        <h1 class="text-base font-extrabold text-slate-900 tracking-tight leading-none">
                            @yield('title_header', 'Trang Quản Trị Hệ Thống')
                        </h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#B2D5E5]/40 text-slate-800 border border-[#B2D5E5]">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ $roleName }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right: Actions & Utilities -->
            <div class="flex items-center gap-3">
                <!-- Live Date Indicator -->
                <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-600">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ \Carbon\Carbon::now()->locale('vi')->translatedFormat('l, d/m/Y') }}</span>
                </div>

                <!-- Store Preview Button -->
                <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-50 hover:bg-[#B2D5E5] text-slate-700 hover:text-slate-900 border border-slate-200 text-xs font-bold transition-all shadow-xs group" title="Mở trang chủ bán hàng (Tab mới)">
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-900 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    <span>Xem cửa hàng</span>
                </a>

                <!-- Notification Bell for Pending Orders -->
                @php
                    $pendingCountHeader = \App\Models\Order::where('order_status', 'pending')->count();
                @endphp
                <a href="{{ route('admin.orders.index') }}" class="relative p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-all border border-slate-200 bg-white" title="{{ $pendingCountHeader > 0 ? $pendingCountHeader . ' đơn hàng chờ duyệt' : 'Không có đơn chờ duyệt' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    @if($pendingCountHeader > 0)
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-red-500 text-white text-[9px] font-black flex items-center justify-center border-2 border-white shadow-xs">
                            {{ $pendingCountHeader > 9 ? '9+' : $pendingCountHeader }}
                        </span>
                    @endif
                </a>
            </div>
        </header>

        <!-- Floating Top-Right Toast Notification -->
        <div id="adminToastContainer" class="fixed top-6 right-6 z-50 flex flex-col gap-3 pointer-events-none max-w-sm w-full">
            @if(session('success'))
                <div id="toastSuccess" class="pointer-events-auto flex items-start gap-3 p-4 bg-white/95 backdrop-blur-md border border-emerald-200 text-slate-800 rounded-2xl shadow-xl shadow-emerald-500/10 transition-all duration-300 transform translate-y-0 opacity-100">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0 pr-1">
                        <h4 class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Thành công</h4>
                        <p class="text-xs font-semibold text-slate-700 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="dismissToast('toastSuccess')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100 transition-colors flex-shrink-0" title="Đóng">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div id="toastError" class="pointer-events-auto flex items-start gap-3 p-4 bg-white/95 backdrop-blur-md border border-red-200 text-slate-800 rounded-2xl shadow-xl shadow-red-500/10 transition-all duration-300 transform translate-y-0 opacity-100">
                    <div class="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0 pr-1">
                        <h4 class="text-xs font-bold text-red-700 uppercase tracking-wider">Thông báo lỗi</h4>
                        <p class="text-xs font-semibold text-slate-700 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                    </div>
                    <button type="button" onclick="dismissToast('toastError')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100 transition-colors flex-shrink-0" title="Đóng">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif
            @if(session('info'))
                <div id="toastInfo" class="pointer-events-auto flex items-start gap-3 p-4 bg-white/95 backdrop-blur-md border border-amber-200 text-slate-800 rounded-2xl shadow-xl shadow-amber-500/10 transition-all duration-300 transform translate-y-0 opacity-100">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0 pr-1">
                        <h4 class="text-xs font-bold text-amber-700 uppercase tracking-wider">Thông báo hệ thống</h4>
                        <p class="text-xs font-semibold text-slate-700 mt-0.5 leading-relaxed">{{ session('info') }}</p>
                    </div>
                    <button type="button" onclick="dismissToast('toastInfo')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100 transition-colors flex-shrink-0" title="Đóng">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif
        </div>

        <script>
            function dismissToast(id) {
                const el = document.getElementById(id);
                if (el) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-12px) scale(0.95)';
                    setTimeout(() => el.remove(), 300);
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                ['toastSuccess', 'toastError', 'toastInfo'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        setTimeout(() => dismissToast(id), 5000);
                    }
                });
            });
        </script>

        <!-- Dynamic Main Content -->
        <main id="adminMainContent" class="flex-1 overflow-y-auto px-6 md:px-8 pb-8 pt-0 transition-all duration-150">
            @yield('content')
        </main>
    </div>

    <!-- Admin SPA Page Transition Engine -->
    <script src="{{ asset('js/admin-spa.js') }}"></script>
    <!-- Admin Live Search Engine -->
    <script src="{{ asset('js/admin-live-search.js') }}"></script>
    <!-- Admin Sticky Bar Engine (Mô phỏng cấu trúc chuyển động như trang chi tiết sản phẩm) -->
    <script src="{{ asset('js/admin-sticky-bar.js') }}"></script>
    @stack('scripts')
</body>
</html>
