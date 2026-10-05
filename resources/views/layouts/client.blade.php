@php
    $unreadNotificationsCount = auth()->check() ? auth()->user()->notifications()->whereNull('read_at')->count() : 0;
    $recentNotifications = auth()->check() ? auth()->user()->notifications()->latest()->limit(5)->get() : collect();
@endphp
<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GiaDụng - Thiết Bị Gia Dụng Cao Cấp')</title>
    <meta name="description" content="Hệ thống bán lẻ thiết bị gia dụng chính hãng, uy tín hàng đầu Việt Nam. Nồi, chảo, nồi cơm điện, máy xay sinh tố cao cấp.">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Hiệu ứng đẩy tách không gian cho vật thể nổi 3D */
        .product-push-card {
            transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.4s ease;
            will-change: transform, opacity;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .animate-float-slow {
            animation: float 4s ease-in-out infinite;
        }
        .animate-float-fast {
            animation: float 2.5s ease-in-out infinite;
        }
        .animate-float-delayed {
            animation: float 3.5s ease-in-out 1s infinite;
        }
        #hero-parallax-stage {
            min-height: 580px;
            height: 640px;
        }
        @media (max-width: 1024px) {
            #hero-parallax-stage {
                height: 620px;
            }
        }
        @media (max-width: 640px) {
            #hero-parallax-stage {
                height: 580px;
            }
        }
        /* Smart Dynamic Navbar Search (for Products Page) */
        .navbar-search-smart-transition {
            transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        max-width 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                        margin 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: opacity, transform, max-width;
        }
        .navbar-search-smart-collapsed {
            opacity: 0 !important;
            pointer-events: none !important;
            visibility: hidden !important;
            max-width: 0 !important;
            margin-right: -0.75rem !important;
            transform: scale(0.92) translateX(8px) !important;
            overflow: hidden !important;
        }
        .navbar-search-smart-expanded {
            opacity: 1 !important;
            pointer-events: auto !important;
            visibility: visible !important;
            max-width: 320px !important;
            margin-right: 0 !important;
            transform: scale(1) translateX(0) !important;
            overflow: visible !important;
        }
        /* Đổ bóng tối đậm đà, rõ nét thấy rõ từng khối của Card sản phẩm */
        .product-card-3d {
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.22), 0 4px 10px rgba(0, 0, 0, 0.12) !important;
            border: 1px solid rgba(226, 232, 240, 0.95) !important;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease !important;
        }
        .product-card-3d:hover {
            box-shadow: 0 22px 40px rgba(0, 0, 0, 0.32), 0 8px 16px rgba(0, 0, 0, 0.18) !important;
            border-color: rgba(203, 213, 225, 1) !important;
        }
    </style>
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Force light mode
        document.documentElement.classList.remove('dark');
        document.cookie = 'theme=light; path=/; max-age=31536000';
    </script>
</head>
<body class="flex flex-col min-h-screen bg-slate-50 text-slate-800 transition-colors duration-300">

    <!-- Header Navbar -->
    @if(request()->routeIs('home', 'home.preview'))
        <header id="main-header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-white/15 dark:bg-slate-950/20 backdrop-blur-sm border-b border-white/10 dark:border-white/5 shadow-none">
    @else
        <header id="main-header" class="sticky top-0 z-40 bg-white/80 dark:bg-slate-950/80 backdrop-blur-md border-b border-slate-100 dark:border-slate-800 shadow-sm transition-colors duration-300">
    @endif
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                        <img src="{{ asset('images/logo-transparent.png') }}" alt="GiaDụng" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform duration-300">
                        <span class="font-extrabold text-xl tracking-tight text-slate-900">GiaDụng</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('products.index') }}" class="text-slate-600 dark:text-white hover:text-orange-700 dark:hover:text-orange-400 font-medium hover:scale-105 transition-all duration-300">Sản phẩm</a>
                    <a href="{{ route('games.index') }}" class="text-slate-600 dark:text-white hover:text-orange-700 dark:hover:text-orange-400 font-medium hover:scale-105 transition-all duration-300">Sự kiện</a>
                    <a href="{{ route('news.index') }}" class="text-slate-600 dark:text-white hover:text-orange-700 dark:hover:text-orange-400 font-medium hover:scale-105 transition-all duration-300">Tin tức</a>
                    <a href="{{ route('about') }}" class="text-slate-600 dark:text-white hover:text-orange-700 dark:hover:text-orange-400 font-medium hover:scale-105 transition-all duration-300">Về chúng tôi</a>
                </nav>

                <!-- Actions -->
                <div class="flex items-center gap-2 sm:gap-3 lg:gap-4">

                    <!-- Search Bar (Always visible in navbar, smart hidden on products page until scrolled) -->
                    @php
                        $isProductsIndex = request()->routeIs('products.index');
                    @endphp
                    <form id="navbar-search-form" action="{{ route('products.index') }}" method="GET" 
                          class="relative flex items-center search-container-with-history {{ $isProductsIndex ? 'navbar-search-smart-transition navbar-search-smart-collapsed' : '' }}">
                        <div class="relative flex items-center">
                            <input type="text"
                                   name="q"
                                   id="navbar-search-input"
                                   value="{{ request('q') }}"
                                   placeholder="Tìm kiếm sản phẩm..."
                                   autocomplete="off"
                                   class="w-32 sm:w-44 md:w-52 lg:w-64 pl-9 pr-7 py-1.5 text-xs font-medium bg-slate-100 dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 focus:bg-white dark:focus:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 shadow-sm transition-all duration-200"
                                   style="border-radius: 9999px !important; padding-left: 2.25rem !important;">
                            <button type="submit" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-500 transition-colors flex items-center justify-center p-0.5" title="Tìm kiếm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                            @if(request('q'))
                                <a href="{{ route('products.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition-colors flex items-center justify-center p-0.5" title="Xóa tìm kiếm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                        @include('partials.search-history-dropdown', ['dropdownClass' => 'w-72 sm:w-80 md:w-96 right-0 sm:left-0 sm:right-auto'])
                    </form>

                    <!-- Notifications Dropdown -->
                    @auth
                        <div class="relative group">
                            <button class="relative p-2 text-slate-600 dark:text-slate-300 hover:text-orange-500 transition-colors focus:outline-none">
                                @if($unreadNotificationsCount > 0)
                                    <span class="absolute top-1.5 right-1.5 bg-red-500 text-white text-[10px] font-bold rounded-full w-4.5 h-4.5 flex items-center justify-center border border-white dark:border-slate-900">
                                        {{ $unreadNotificationsCount }}
                                    </span>
                                @endif
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-72 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl shadow-xl py-2 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50 rounded-t-2xl">
                                    <span class="font-bold text-xs">Thông báo</span>
                                    <form action="{{ route('notifications.read-all') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[10px] text-orange-500 hover:underline">Đọc tất cả</button>
                                    </form>
                                </div>
                                <div class="max-h-64 overflow-y-auto">
                                    @forelse($recentNotifications as $notif)
                                        <a href="{{ route('notifications.read', $notif->id) }}" class="block px-4 py-2.5 text-xs hover:bg-slate-50 dark:hover:bg-slate-700 border-b border-slate-50 dark:border-slate-700/50 last:border-none {{ $notif->read_at ? 'opacity-60' : 'font-semibold' }}">
                                            <p class="text-slate-800 dark:text-slate-200 line-clamp-1">{{ $notif->title }}</p>
                                            <p class="text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5">{{ $notif->content }}</p>
                                            <span class="text-[9px] text-slate-400 block mt-1">{{ $notif->created_at->diffForHumans() }}</span>
                                        </a>
                                    @empty
                                        <div class="px-4 py-6 text-center text-slate-400 dark:text-slate-500 text-xs">
                                            Không có thông báo nào.
                                        </div>
                                    @endforelse
                                </div>
                                <a href="{{ route('notifications.index') }}" class="block text-center py-2 text-xs font-semibold text-orange-500 border-t border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-b-2xl">Xem tất cả thông báo</a>
                            </div>
                        </div>
                    @endauth

                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-slate-700 dark:text-slate-200 hover:text-orange-500 transition-colors group" title="Giỏ hàng">
                        <span id="header-cart-badge" class="absolute -top-1 -right-1 bg-[#B2D5E5] text-slate-800 text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center border-2 border-white dark:border-slate-900 shadow-sm transition-all duration-300">
                            {{ auth()->check() && auth()->user()->cart ? auth()->user()->cart->items->sum('quantity') : array_sum(array_column(session('cart', []), 'quantity')) }}
                        </span>
                        <svg class="w-6 h-6 group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </a>

                    <!-- Account / Auth Menu -->
                    @auth
                        <div class="relative group">
                            <button class="flex items-center gap-1.5 focus:outline-none py-1">
                                <div class="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400 font-bold flex items-center justify-center shadow-inner">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <span class="hidden lg:inline text-sm font-medium text-slate-700 dark:text-slate-300 truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                            </button>
                            <!-- Dropdown -->
                            <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl shadow-lg py-1.5 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50">
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-orange-500 font-medium">Trang Quản Trị</a>
                                @endif
                                <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-orange-500">Thông tin cá nhân</a>
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-orange-500">Đơn hàng của tôi</a>
                                <a href="{{ route('friends.index') }}" class="block px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-orange-500">Danh sách bạn bè</a>
                                <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30">Đăng xuất</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="p-2 text-slate-600 dark:text-slate-300 hover:text-orange-500 transition-colors" title="Đăng nhập / Đăng ký">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </a>
                    @endauth

                    <!-- Settings Dropdown (Gear Icon) -->
                    <div class="relative group">
                        <button class="p-2 text-slate-600 dark:text-slate-300 hover:text-orange-500 transition-colors focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-xl shadow-lg py-2 invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 z-50 text-xs">
                            <span class="block px-4 py-1.5 text-slate-400 uppercase font-bold tracking-widest text-[9px]">Hệ thống</span>
                            <button onclick="openReportModal()" class="w-full text-left px-4 py-2 hover:bg-slate-50 text-slate-700">Báo cáo sự cố</button>
                            <a href="{{ route('about') }}" class="block px-4 py-2 hover:bg-slate-50 text-slate-700">Chính sách cửa hàng</a>
                            @auth
                                <a href="{{ route('friends.index') }}" class="block px-4 py-2 hover:bg-slate-50 text-slate-700">Hệ thống bạn bè</a>
                            @endauth
                        </div>
                    </div>

                    <!-- Mobile Menu Trigger -->
                    <button id="mobile-menu-btn" class="md:hidden p-2 text-slate-600 dark:text-slate-300 hover:text-orange-500 transition-colors focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Collapsible Search Bar on Mobile (< sm) -->
        <div id="mobile-search-bar" class="hidden sm:hidden px-4 pb-3 pt-2 border-t border-slate-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md">
            <form action="{{ route('products.index') }}" method="GET" class="relative">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Tìm kiếm sản phẩm..."
                       autocomplete="off"
                       class="w-full pl-9 pr-8 py-2 text-xs font-medium bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-slate-800 dark:text-slate-100 placeholder-slate-400">
                <button type="submit" class="absolute left-3 top-2.5 text-slate-400 hover:text-orange-500" title="Tìm kiếm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
                @if(request('q'))
                    <a href="{{ route('products.index') }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-red-500" title="Xóa">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </form>
        </div>

        <!-- Mobile Drawer -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-950 px-4 py-4 space-y-3 shadow-inner">
            <!-- Mobile Menu Search -->
            <form action="{{ route('products.index') }}" method="GET" class="relative mb-2">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Tìm kiếm sản phẩm..."
                       autocomplete="off"
                       class="w-full pl-9 pr-8 py-2 text-xs font-medium bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-slate-800 dark:text-slate-100 placeholder-slate-400">
                <button type="submit" class="absolute left-3 top-2.5 text-slate-400 hover:text-orange-500" title="Tìm kiếm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
                @if(request('q'))
                    <a href="{{ route('products.index') }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-red-500" title="Xóa">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </form>

            <a href="{{ route('products.index') }}" class="block py-2 text-slate-700 dark:text-slate-300 hover:text-orange-500 font-medium">Sản phẩm</a>
            <a href="{{ route('games.index') }}" class="block py-2 text-slate-700 dark:text-slate-300 hover:text-orange-500 font-medium">Sự kiện</a>
            <a href="{{ route('news.index') }}" class="block py-2 text-slate-700 dark:text-slate-300 hover:text-orange-500 font-medium">Tin tức</a>
            <a href="{{ route('about') }}" class="block py-2 text-slate-700 dark:text-slate-300 hover:text-orange-500 font-medium">Về chúng tôi</a>
            
            <div class="border-t border-slate-100 dark:border-slate-800 my-2 pt-2"></div>
            @guest
                <a href="{{ route('login') }}" class="block py-2 text-slate-700 dark:text-slate-300 hover:text-orange-500">Đăng nhập</a>
                <a href="{{ route('register') }}" class="block py-2 text-orange-500 font-semibold">Đăng ký tài khoản</a>
            @endguest
        </div>
    </header>

    <!-- Floating Top-Right Toast Notification for Client -->
    <div id="clientToastContainer" class="fixed top-20 right-4 sm:right-6 z-50 flex flex-col gap-3 pointer-events-none max-w-sm w-full">
        @if(session('success'))
            <div id="clientToastSuccess" class="pointer-events-auto flex items-start gap-3 p-4 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-emerald-200 dark:border-emerald-800/60 text-slate-800 dark:text-slate-100 rounded-2xl shadow-xl shadow-emerald-500/10 transition-all duration-300 transform translate-y-0 opacity-100">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1 min-w-0 pr-1">
                    <h4 class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Thành công</h4>
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                </div>
                <button type="button" onclick="dismissClientToast('clientToastSuccess')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex-shrink-0" title="Đóng">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div id="clientToastError" class="pointer-events-auto flex items-start gap-3 p-4 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border border-red-200 dark:border-red-800/60 text-slate-800 dark:text-slate-100 rounded-2xl shadow-xl shadow-red-500/10 transition-all duration-300 transform translate-y-0 opacity-100">
                <div class="w-8 h-8 rounded-xl bg-red-100 dark:bg-red-950/50 text-red-600 dark:text-red-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
                <div class="flex-1 min-w-0 pr-1">
                    <h4 class="text-xs font-bold text-red-700 dark:text-red-400 uppercase tracking-wider">Thông báo</h4>
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                </div>
                <button type="button" onclick="dismissClientToast('clientToastError')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex-shrink-0" title="Đóng">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif
    </div>

    <script>
        function dismissClientToast(id) {
            const el = document.getElementById(id);
            if (el) {
                el.style.opacity = '0';
                el.style.transform = 'translateY(-12px) scale(0.95)';
                setTimeout(() => el.remove(), 300);
            }
        }

        function showClientToast(message, type = 'success') {
            const container = document.getElementById('clientToastContainer');
            if (!container) return;
            const toastId = 'toast-' + Date.now();
            const isSuccess = type === 'success';
            const borderColor = isSuccess ? 'border-emerald-200 dark:border-emerald-800/60' : 'border-red-200 dark:border-red-800/60';
            const iconBg = isSuccess ? 'bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-950/50 text-red-600 dark:text-red-400';
            const titleColor = isSuccess ? 'text-emerald-700 dark:text-emerald-400' : 'text-red-700 dark:text-red-400';
            const titleText = isSuccess ? 'Thành công' : 'Thông báo';
            const iconSvg = isSuccess 
                ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>'
                : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>';

            const toast = document.createElement('div');
            toast.id = toastId;
            toast.className = `pointer-events-auto flex items-start gap-3 p-4 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border ${borderColor} text-slate-800 dark:text-slate-100 rounded-2xl shadow-xl transition-all duration-300 transform translate-y-0 opacity-100`;
            toast.innerHTML = `
                <div class="w-8 h-8 rounded-xl ${iconBg} flex items-center justify-center flex-shrink-0 mt-0.5">
                    ${iconSvg}
                </div>
                <div class="flex-1 min-w-0 pr-1">
                    <h4 class="text-xs font-bold ${titleColor} uppercase tracking-wider">${titleText}</h4>
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-0.5 leading-relaxed">${message}</p>
                </div>
                <button type="button" onclick="dismissClientToast('${toastId}')" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors flex-shrink-0" title="Đóng">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            `;
            container.appendChild(toast);
            setTimeout(() => dismissClientToast(toastId), 4000);
        }

        function updateHeaderCartCount(count) {
            const badge = document.getElementById('header-cart-badge');
            if (badge) {
                badge.innerText = count;
                badge.classList.remove('hidden');
                badge.classList.add('scale-125');
                setTimeout(() => badge.classList.remove('scale-125'), 300);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            ['clientToastSuccess', 'clientToastError'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    setTimeout(() => dismissClientToast(id), 4000);
                }
            });

            // Global listener for AJAX cart forms
            document.addEventListener('submit', function(e) {
                const form = e.target.closest('form.ajax-cart-form');
                if (!form) return;
                if (form.method.toUpperCase() === 'GET') return; // Redirects if attributes/options needed

                e.preventDefault();
                const btn = form.querySelector('button[type="submit"]');
                const originalHtml = btn ? btn.innerHTML : '';
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `
                        <svg class="w-4 h-4 animate-spin inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Đang thêm...
                    `;
                }

                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(async res => {
                    const data = await res.json();
                    if (!res.ok) {
                        throw new Error(data.message || 'Lỗi thêm vào giỏ hàng');
                    }
                    return data;
                })
                .then(data => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = `
                            <svg class="w-4 h-4 inline-block mr-1 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            Đã thêm vào giỏ!
                        `;
                        setTimeout(() => {
                            btn.innerHTML = originalHtml;
                        }, 2000);
                    }

                    if (data.cart_count !== undefined) {
                        updateHeaderCartCount(data.cart_count);
                    }

                    showClientToast(data.message || 'Đã thêm sản phẩm vào giỏ hàng thành công!', 'success');
                })
                .catch(err => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    }
                    showClientToast(err.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng.', 'error');
                });
            });
        });
    </script>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-[var(--footer-bg)] text-[var(--footer-text)] mt-20 border-t border-[var(--footer-border)] relative">
        <!-- Floating Top Commitment Bar (Thẻ nổi VIP rực rỡ sang trọng ở cả 2 chế độ) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -translate-y-1/2 relative z-20">
            <div id="footer-commitment-bar" class="bg-gradient-to-r from-[#B2D5E5] via-[#cce7f5] to-[#B2D5E5] dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 border-2 border-white/80 dark:border-candy-blue/40 rounded-3xl shadow-2xl dark:shadow-[0_10px_35px_rgba(178,213,229,0.25)] p-5 sm:p-6 backdrop-blur-xl transition-all duration-300">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center md:text-left divide-y md:divide-y-0 md:divide-x divide-onyx/10 dark:divide-slate-700/60">
                    <div class="flex items-center justify-center md:justify-start gap-3.5 pt-2 md:pt-0 md:px-3">
                        <div class="p-3 bg-white/80 dark:bg-candy-blue/15 text-onyx dark:text-candy-blue rounded-2xl shrink-0 shadow-md border border-white/90 dark:border-candy-blue/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-onyx dark:text-white uppercase tracking-wider">Giao Hàng Hỏa Tốc</h4>
                            <p class="text-[11px] text-onyx/80 dark:text-slate-300 font-semibold">Giao toàn quốc nhanh chóng</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-center md:justify-start gap-3.5 pt-4 md:pt-0 md:px-3">
                        <div class="p-3 bg-white/80 dark:bg-candy-blue/15 text-onyx dark:text-candy-blue rounded-2xl shrink-0 shadow-md border border-white/90 dark:border-candy-blue/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-onyx dark:text-white uppercase tracking-wider">Bảo Hành Chính Hãng</h4>
                            <p class="text-[11px] text-onyx/80 dark:text-slate-300 font-semibold">Cam kết 100% chất lượng</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-center md:justify-start gap-3.5 pt-4 md:pt-0 md:px-3">
                        <div class="p-3 bg-white/80 dark:bg-candy-blue/15 text-onyx dark:text-candy-blue rounded-2xl shrink-0 shadow-md border border-white/90 dark:border-candy-blue/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-onyx dark:text-white uppercase tracking-wider">Thanh Toán An Toàn</h4>
                            <p class="text-[11px] text-onyx/80 dark:text-slate-300 font-semibold">Bảo mật thông tin tối đa</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-center md:justify-start gap-3.5 pt-4 md:pt-0 md:px-3">
                        <div class="p-3 bg-white/80 dark:bg-candy-blue/15 text-onyx dark:text-candy-blue rounded-2xl shrink-0 shadow-md border border-white/90 dark:border-candy-blue/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-onyx dark:text-white uppercase tracking-wider">Hỗ Trợ 24/7</h4>
                            <p class="text-[11px] text-onyx/80 dark:text-slate-300 font-semibold">Tư vấn tận tâm nhiệt tình</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Footer Content -->
        <div id="footer-main-content" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 pb-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                
                <!-- Brand Info Column -->
                <div class="md:col-span-5 space-y-4">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                        <img src="{{ asset('images/logo-transparent.png') }}" alt="GiaDụngPro" class="h-10 w-auto object-contain group-hover:scale-105 transition-transform duration-300">
                        <span class="font-black text-2xl tracking-tight text-[var(--footer-heading)]">GiaDụng<span class="text-sky-500">Pro</span></span>
                    </a>
                    <p class="text-sm text-[var(--footer-text)] leading-relaxed opacity-90 max-w-md">
                        GiaDụng mang đến giải pháp tốt nhất cho căn nhà của bạn. Các sản phẩm gia dụng hiện đại, thông minh chất lượng cao giúp tiết kiệm thời gian và nâng tầm cuộc sống.
                    </p>
                    <!-- Badges / Social Icons -->
                    <div class="flex items-center gap-3 pt-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-[var(--footer-heading)] opacity-80">Kết nối:</span>
                        <div class="flex items-center gap-2">
                            <a href="#" class="w-8 h-8 rounded-full bg-slate-900/10 dark:bg-white/10 flex items-center justify-center text-[var(--footer-heading)] hover:bg-orange-500 hover:text-white transition-all duration-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="w-8 h-8 rounded-full bg-slate-900/10 dark:bg-white/10 flex items-center justify-center text-[var(--footer-heading)] hover:bg-orange-500 hover:text-white transition-all duration-300">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Navigation Column -->
                <div class="md:col-span-3 space-y-4">
                    <div class="relative inline-block">
                        <h3 class="text-[var(--footer-heading)] font-extrabold text-base uppercase tracking-wider">Khám Phá</h3>
                        <div class="w-8 h-0.5 bg-orange-500 rounded-full mt-1"></div>
                    </div>
                    <ul class="space-y-2.5 text-sm">
                        <li>
                            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 hover:translate-x-1 hover:text-orange-500 transition-all">
                                <span class="text-orange-500 text-xs">▸</span> Tất cả sản phẩm
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('games.index') }}" class="inline-flex items-center gap-2 hover:translate-x-1 hover:text-orange-500 transition-all">
                                <span class="text-orange-500 text-xs">▸</span> Sự kiện nhận thưởng
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 hover:translate-x-1 hover:text-orange-500 transition-all">
                                <span class="text-orange-500 text-xs">▸</span> Bài viết tin tức
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('about') }}" class="inline-flex items-center gap-2 hover:translate-x-1 hover:text-orange-500 transition-all">
                                <span class="text-orange-500 text-xs">▸</span> Về GiaDụng
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info Column -->
                <div class="md:col-span-4 space-y-4">
                    <div class="relative inline-block">
                        <h3 class="text-[var(--footer-heading)] font-extrabold text-base uppercase tracking-wider">Liên Hệ</h3>
                        <div class="w-8 h-0.5 bg-orange-500 rounded-full mt-1"></div>
                    </div>
                    <ul class="space-y-3 text-sm text-[var(--footer-text)]">
                        <li class="flex items-start gap-3">
                            <div class="p-1.5 bg-orange-500/10 text-orange-500 rounded-lg shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <span><strong class="text-[var(--footer-heading)]">Địa chỉ:</strong> 123 Đường Laragon, Quận Cầu Giấy, Hà Nội</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="p-1.5 bg-orange-500/10 text-orange-500 rounded-lg shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <span><strong class="text-[var(--footer-heading)]">Điện thoại:</strong> 0123.456.789</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="p-1.5 bg-orange-500/10 text-orange-500 rounded-lg shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <span><strong class="text-[var(--footer-heading)]">Email:</strong> hotro@giadungpro.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="border-t border-[var(--footer-border)] mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[var(--footer-text)] opacity-85">
                <p>&copy; {{ date('Y') }} GiaDụng. Tất cả các quyền được bảo lưu. Dự án phát triển trên Laragon Windows.</p>
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 bg-slate-900/10 dark:bg-white/10 rounded-full text-[10px] font-bold uppercase tracking-widest text-[var(--footer-heading)] border border-[var(--footer-border)]">
                        ✦ Premium Quality ✦
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Report Modal -->
    <div id="report-modal" class="fixed inset-0 z-50 items-center justify-center bg-slate-900/60 backdrop-blur-xs hidden">
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 max-w-md w-full mx-4 shadow-2xl border border-slate-100 dark:border-slate-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-lg">Báo cáo Sự cố & Phản hồi</h3>
                <button onclick="closeReportModal()" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('settings.report') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Loại phản hồi</label>
                        <select name="type" class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="system">Lỗi hệ thống</option>
                            <option value="product">Sản phẩm</option>
                            <option value="review">Đánh giá sản phẩm</option>
                            <option value="comment">Bình luận</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Tiêu đề / Lý do</label>
                        <input type="text" name="reason" placeholder="VD: Lỗi thanh toán, Sản phẩm sai mô tả..." required class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase mb-1">Nội dung chi tiết</label>
                        <textarea name="content" rows="4" placeholder="Nhập phản hồi chi tiết của bạn tại đây..." required class="w-full bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl px-3 py-2 text-sm text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="closeReportModal()" class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-700">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-semibold shadow">Gửi báo cáo</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Toggle Mobile Menu
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Toggle Theme (Light/Dark) with Cookie & DB Sync

        // Modal triggers
        function openReportModal() {
            document.getElementById('report-modal').classList.remove('hidden');
            document.getElementById('report-modal').classList.add('flex');
        }
        function closeReportModal() {
            document.getElementById('report-modal').classList.add('hidden');
            document.getElementById('report-modal').classList.remove('flex');
        }

        // --- INTERACTIVE QUICK VIEW & EXPANDED CARD MODAL ---
        function triggerQuickView(productId, event) {
            // Prevent opening quick view if clicking directly on link elements or buttons inside card
            if (event && event.target.closest('a, button, form')) {
                return;
            }

            const modal = document.getElementById('quick-view-modal');
            const loading = document.getElementById('qv-loading');
            const content = document.getElementById('qv-content');
            const modalBox = document.getElementById('qv-modal-box');

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            content.classList.add('hidden');
            document.body.classList.add('overflow-hidden');

            fetch(`/products/${productId}/quick-view`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('qv-image').src = data.thumbnail;
                        document.getElementById('qv-brand').innerText = data.brand;
                        document.getElementById('qv-title').innerText = data.name;
                        document.getElementById('qv-price').innerText = data.sale_price ? data.sale_price : data.price;
                        
                        const originalPriceEl = document.getElementById('qv-sale-price');
                        if (data.sale_price) {
                            originalPriceEl.innerText = data.price;
                            originalPriceEl.classList.remove('hidden');
                        } else {
                            originalPriceEl.classList.add('hidden');
                        }

                        const badgeEl = document.getElementById('qv-discount-badge');
                        if (data.discount_percent > 0) {
                            document.getElementById('qv-discount-val').innerText = data.discount_percent;
                            badgeEl.classList.remove('hidden');
                        } else {
                            badgeEl.classList.add('hidden');
                        }

                        document.getElementById('qv-input-product-id').value = data.id;
                        document.getElementById('qv-description').innerText = data.short_description || data.description;
                        document.getElementById('qv-rating-text').innerText = data.avg_rating;
                        document.getElementById('qv-reviews-count').innerText = data.total_reviews;
                        document.getElementById('qv-detail-link').href = data.detail_url;

                        // Colors list
                        const colorsContainer = document.getElementById('qv-colors-list');
                        colorsContainer.innerHTML = '';
                        if (data.colors && data.colors.length > 0) {
                            data.colors.forEach(c => {
                                const span = document.createElement('span');
                                span.className = 'px-3 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-600 flex items-center gap-1.5';
                                span.innerHTML = `<span class="w-3.5 h-3.5 rounded-full border shadow-xs" style="background-color: ${c.hex_code || '#ff6802'}"></span> ${c.name}`;
                                colorsContainer.appendChild(span);
                            });
                            document.getElementById('qv-colors-container').classList.remove('hidden');
                        } else {
                            document.getElementById('qv-colors-container').classList.add('hidden');
                        }

                        const qvForm = document.getElementById('qv-cart-form');
                        const qvBtn = qvForm ? qvForm.querySelector('button[type="submit"]') : null;
                        if (qvForm && qvBtn) {
                            qvForm.action = "{{ route('cart.add') }}";
                            qvForm.method = 'POST';
                            qvBtn.innerHTML = `
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Thêm vào giỏ
                            `;
                        }

                        loading.classList.add('hidden');
                        content.classList.remove('hidden');
                        modalBox.classList.remove('scale-95', 'opacity-0');
                        modalBox.classList.add('scale-100', 'opacity-100');
                    }
                })
                .catch(err => {
                    console.error(err);
                    closeQuickViewModal();
                });
        }

        function closeQuickViewModal() {
            const modal = document.getElementById('quick-view-modal');
            const modalBox = document.getElementById('qv-modal-box');
            if (modalBox) {
                modalBox.classList.remove('scale-100', 'opacity-100');
                modalBox.classList.add('scale-95', 'opacity-0');
            }
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 150);
        }
    </script>

    <!-- INTERACTIVE EXPANDED QUICK VIEW CARD MODAL (Đúng sơ đồ minh họa) -->
    <div id="quick-view-modal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 overflow-y-auto transition-all duration-300">
        <div id="qv-modal-box" class="relative bg-white dark:bg-slate-900 border-2 border-slate-900 dark:border-slate-700 rounded-3xl shadow-2xl max-w-5xl w-full p-6 sm:p-8 text-left transition-all duration-300 transform scale-95 opacity-0">
            
            <!-- Close Button -->
            <button onclick="closeQuickViewModal()" class="absolute top-4 right-4 p-2 bg-slate-100 dark:bg-slate-800 hover:bg-red-500 text-slate-500 hover:text-white rounded-full transition-colors z-20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Loading Spinner -->
            <div id="qv-loading" class="py-16 text-center">
                <div class="inline-block w-10 h-10 border-4 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-xs font-bold text-slate-500 mt-3">Đang tải thông tin sản phẩm...</p>
            </div>

            <!-- Content Grid (Matches User Diagram: Card Box on Left, Detailed Info Panel on Right) -->
            <div id="qv-content" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- CỘT TRÁI: REPLICATE THẺ SẢN PHẨM (MÔ HÌNH BÊN TRÁI HÌNH MINH HỌA) -->
                <div class="lg:col-span-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-3xl p-5 shadow-lg relative flex flex-col justify-between space-y-4">
                    <!-- Discount Badge -->
                    <div id="qv-discount-badge" class="absolute top-4 left-4 bg-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow z-10 hidden">
                        Giảm <span id="qv-discount-val">0</span>%
                    </div>

                    <!-- Image -->
                    <div class="aspect-square w-full bg-slate-50 dark:bg-slate-900 rounded-2xl overflow-hidden relative border border-slate-100 dark:border-slate-700 flex items-center justify-center">
                        <img id="qv-image" src="" alt="" class="w-full h-full object-cover">
                    </div>

                    <!-- Info -->
                    <div class="space-y-2">
                        <span id="qv-brand" class="text-xs font-bold text-slate-400 uppercase tracking-wider block">SUNHOUSE</span>
                        <h3 id="qv-title" class="font-extrabold text-base leading-snug text-slate-900 metallic-dark-text">Nồi lẩu điện đa năng</h3>
                        
                        <div class="flex items-baseline gap-2 pt-1">
                            <span id="qv-price" class="text-2xl font-extrabold text-[#0284c7] dark:text-[#38bdf8]">720.000 ₫</span>
                            <span id="qv-sale-price" class="text-xs text-slate-400 line-through">850.000 ₫</span>
                        </div>
                    </div>

                    <!-- Quick Add Button -->
                    <form id="qv-cart-form" action="{{ route('cart.add') }}" method="POST" class="ajax-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" id="qv-input-product-id" value="">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="w-full py-3 bg-cyan-200 hover:bg-cyan-300 dark:bg-cyan-800/60 dark:hover:bg-cyan-700 text-slate-900 dark:text-white text-xs font-extrabold rounded-2xl transition-all shadow flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Thêm vào giỏ
                        </button>
                    </form>
                </div>

                <!-- CỘT PHẢI: BẢNG "THÔNG TIN CỤ THỂ HƠN CỦA SẢN PHẨM" (MÔ HÌNH BÊN PHẢI HÌNH MINH HỌA) -->
                <div class="lg:col-span-7 space-y-5 text-left border-l-0 lg:border-l border-slate-100 dark:border-slate-800 lg:pl-8">
                    
                    <!-- Header Bar matching user's red arrow indicator -->
                    <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div class="p-2 bg-red-500/10 text-red-500 rounded-xl">
                            <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-black uppercase tracking-wider text-slate-900 dark:text-white">THÔNG TIN CỤ THỂ HƠN CỦA SẢN PHẨM</h2>
                            <p class="text-xs text-slate-400">Xem nhanh tổng quan tính năng & các màu sắc tùy chọn</p>
                        </div>
                    </div>

                    <!-- Short Description -->
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Mô tả sản phẩm:</span>
                        <p id="qv-description" class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-700/60">
                            Sản phẩm gia dụng chất lượng cao.
                        </p>
                    </div>

                    <!-- Rating & Stock Status -->
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-1 font-bold text-amber-500 bg-amber-50 dark:bg-amber-950/40 px-3 py-1 rounded-full border border-amber-200 dark:border-amber-800">
                            ⭐ <span id="qv-rating-text">5.0</span>
                            <span class="text-slate-400 font-normal">(<span id="qv-reviews-count">0</span> đánh giá)</span>
                        </div>
                        <span id="qv-stock-status" class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 font-bold rounded-full border border-emerald-200 dark:border-emerald-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            Còn hàng
                        </span>
                    </div>

                    <!-- Available Colors -->
                    <div id="qv-colors-container">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Màu sắc có sẵn:</span>
                        <div id="qv-colors-list" class="flex flex-wrap gap-2">
                            <!-- Injected JS -->
                        </div>
                    </div>

                    <!-- Guarantees & Warranties -->
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700 flex items-center gap-2.5">
                            <span class="text-base">🛡️</span>
                            <div>
                                <span class="font-bold text-slate-800 dark:text-slate-200 block">Bảo hành chính hãng</span>
                                <span class="text-slate-400">12 - 24 tháng</span>
                            </div>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700 flex items-center gap-2.5">
                            <span class="text-base">🔄</span>
                            <div>
                                <span class="font-bold text-slate-800 dark:text-slate-200 block">1 Đổi 1 trong 30 ngày</span>
                                <span class="text-slate-400">Lỗi từ nhà sản xuất</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Link to Full Product Page -->
                    <div class="pt-2">
                        <a id="qv-detail-link" href="#" class="w-full py-3.5 bg-orange-500 hover:bg-orange-600 text-slate-950 font-extrabold text-xs rounded-2xl transition-all shadow-lg flex items-center justify-center gap-2">
                            <span>Xem Trang Chi Tiết Đầy Đủ</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @if(request()->routeIs('home', 'home.preview'))
    <script>
        // Fixed Scroll Header Transition for Home Page
        window.addEventListener('scroll', () => {
            const header = document.getElementById('main-header');
            if (!header) return;
            if (window.scrollY > 20) {
                header.classList.add('bg-white/95', 'dark:bg-slate-950/95', 'shadow-md', 'border-slate-200/80', 'dark:border-slate-800/80');
                header.classList.remove('bg-white/15', 'dark:bg-slate-950/20', 'border-white/10', 'dark:border-white/5', 'shadow-none');
            } else {
                header.classList.remove('bg-white/95', 'dark:bg-slate-950/95', 'shadow-md', 'border-slate-200/80', 'dark:border-slate-800/80');
                header.classList.add('bg-white/15', 'dark:bg-slate-950/20', 'border-white/10', 'dark:border-white/5', 'shadow-none');
            }
        });
    </script>
    @endif

    <!-- Search History Interactive Script -->
    <script src="{{ asset('js/search-history.js') }}"></script>
    
    <!-- AI Smart Chat Assistant Widget -->
    @include('components.ai-chat-widget')

    @stack('scripts')
</body>
</html>
