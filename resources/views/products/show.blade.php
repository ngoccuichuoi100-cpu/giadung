@extends('layouts.client')

@section('title', $product->name . ' - GiaDụngPro')

@section('content')
    <div class="page-entrance-smooth max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Breadcrumbs -->
        <nav class="flex text-xs text-slate-500 mb-6 gap-2 text-left" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-orange-500">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-orange-500">{{ $product->category->name }}</a>
            <span>/</span>
            <span class="text-slate-800 dark:text-slate-200 font-medium truncate max-w-[200px]">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl p-8 shadow-sm text-left">
            <!-- Left Column: Image Gallery -->
            <div class="space-y-4">
                <!-- Main Image Display Container -->
                <div class="aspect-square w-full bg-slate-50 dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden relative shadow-inner flex items-center justify-center group">
                    @if($product->thumbnail)
                        <img id="main-image" src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-slate-150 text-slate-400">
                            <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Thumbnails Gallery -->
                @php
                    $validImages = $product->images->filter(function($img) {
                        return !empty($img->image_url);
                    });
                @endphp
                @if($validImages->isNotEmpty())
                    <div class="grid grid-cols-5 gap-3">
                        <button onclick="changeMainImage('{{ $product->thumbnail_url }}')" class="aspect-square bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden focus:outline-none hover:border-orange-500 transition-colors">
                            <img src="{{ $product->thumbnail_url }}" class="w-full h-full object-cover">
                        </button>
                        @foreach($validImages as $img)
                            <button onclick="changeMainImage('{{ $img->image_url }}')" class="aspect-square bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden focus:outline-none hover:border-orange-500 transition-colors">
                                <img src="{{ $img->image_url }}" class="w-full h-full object-cover" onerror="this.closest('button').remove()">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right Column: Product Info -->
            <div class="flex flex-col justify-between">
                <div>
                    <!-- Brand, Code & Wishlist -->
                    <div class="flex justify-between items-center text-xs text-slate-400 dark:text-slate-500 mb-2 font-semibold">
                        <span class="uppercase tracking-wider">{{ $product->brandRelation ? $product->brandRelation->name : ($product->brand ?? 'GiaDụngPro') }}</span>
                        <div class="flex items-center gap-4">
                            <span>Mã SP: <span id="product-sku" class="text-slate-600 dark:text-slate-400 font-bold">{{ $product->code }}</span></span>
                            @auth
                                <button id="wishlist-btn" onclick="toggleWishlist({{ $product->id }})" class="p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-slate-400 hover:text-red-500 focus:outline-none">
                                    @php
                                        $isWishlist = auth()->user()->wishlists()->where('product_id', $product->id)->exists();
                                    @endphp
                                    <svg class="w-6 h-6 {{ $isWishlist ? 'fill-red-500 text-red-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-7.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                            @endauth
                        </div>
                    </div>

                    <!-- Title -->
                    <h1 class="text-3xl font-extrabold leading-tight mb-4 text-slate-900 metallic-dark-text">
                        {{ $product->name }}
                    </h1>

                    <!-- Modern Product Metadata Bar (Chỉ dùng dữ liệu thật từ hệ thống) -->
                    <div class="flex flex-wrap items-center gap-y-2 gap-x-3.5 text-xs mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <!-- Rating & Reviews -->
                        <button type="button" onclick="switchProductTab('reviews', true)" title="Nhấn để xem đánh giá sản phẩm" class="group flex items-center gap-1.5 focus:outline-none">
                            <div class="flex items-center text-amber-400">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <span class="font-bold text-slate-800 dark:text-slate-200 underline decoration-slate-300 dark:decoration-slate-600 underline-offset-2 group-hover:text-orange-600 group-hover:decoration-orange-500 transition-colors">{{ number_format($avgRating ?? 0, 1) }}</span>
                            <span class="text-slate-400 font-normal group-hover:text-orange-500 transition-colors">({{ $totalReviews }} đánh giá)</span>
                        </button>

                        <span class="text-slate-200 dark:text-slate-700">|</span>

                        <!-- Đơn vị tính -->
                        <span class="text-slate-500 dark:text-slate-400">
                            Đơn vị: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $product->unit }}</span>
                        </span>

                        <span class="text-slate-200 dark:text-slate-700">|</span>

                        <!-- Stock Status -->
                        <div id="stock-badge" class="flex items-center gap-1.5 {{ $product->stock_quantity > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} font-medium">
                            <span class="w-2 h-2 rounded-full {{ $product->stock_quantity > 0 ? 'bg-emerald-500' : 'bg-rose-500' }} inline-block"></span>
                            <span>{{ $product->stock_quantity > 0 ? 'Còn hàng sẵn sàng giao' : 'Tạm thời hết hàng' }}</span>
                        </div>

                        <span class="text-slate-200 dark:text-slate-700">|</span>

                        <!-- Origin -->
                        <span class="inline-flex items-center gap-1.5 text-slate-600 dark:text-slate-400 font-medium">
                            <svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ !$product->is_domestic ? 'Hàng ngoại nhập' : 'Hàng nội địa' }}
                        </span>
                    </div>

                    <!-- Modern Pricing Hero (Chuẩn màu nhận diện dự án #0284c7) -->
                    <div id="pricing-container" class="relative mb-6 rounded-2xl bg-gradient-to-r from-sky-500/[0.04] via-candy-blue/10 to-transparent dark:from-slate-900/60 dark:to-slate-800/60 p-5 border border-slate-200/80 dark:border-slate-700 shadow-xs">
                        <div class="flex flex-wrap items-baseline gap-x-3.5 gap-y-2">
                            @if($product->sale_price !== null && $product->sale_price < $product->price)
                                <span id="active-price" class="text-3xl sm:text-4xl font-extrabold text-[#0284c7] dark:text-[#38bdf8] tracking-tight">
                                    {{ number_format($product->sale_price, 0, ',', '.') }} ₫
                                </span>
                                <span id="original-price" class="text-sm sm:text-base font-semibold text-slate-400 line-through">
                                    {{ number_format($product->price, 0, ',', '.') }} ₫
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-orange-500 text-white text-xs font-bold shadow-xs">
                                    -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                </span>
                                <span class="text-xs font-semibold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/60 px-2.5 py-0.5 rounded-md border border-slate-200/60 dark:border-slate-600">
                                    Tiết kiệm {{ number_format($product->price - $product->sale_price, 0, ',', '.') }} ₫
                                </span>
                            @else
                                <span id="active-price" class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                    {{ number_format($product->price, 0, ',', '.') }} ₫
                                </span>
                                <span id="original-price" class="hidden"></span>
                            @endif
                        </div>
                    </div>

                    <!-- Color Swatches & Size Variants Selection -->
                    @if($colors->isNotEmpty() || $sizes->isNotEmpty())
                        <div class="mb-6 space-y-5 bg-slate-50/50 dark:bg-slate-900/30 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <!-- Color Swatches -->
                            @if($colors->isNotEmpty())
                                <div>
                                    <div class="flex items-center justify-between mb-2.5">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Màu sắc sản phẩm</span>
                                        <span id="selected-color-name" class="text-xs font-semibold text-orange-500">Chọn màu</span>
                                    </div>
                                    <div class="flex flex-wrap gap-3">
                                        @foreach($colors as $color)
                                            <button type="button" onclick="selectColor({{ $color->id }}, this)" 
                                                    data-color-name="{{ $color->name }}"
                                                    class="color-btn group/color border-2 border-slate-200 dark:border-slate-700 hover:border-orange-500 dark:hover:border-orange-500 rounded-2xl px-4 py-2 text-xs font-bold transition-all bg-white dark:bg-slate-800 flex items-center gap-2 shadow-xs">
                                                <span class="w-4 h-4 rounded-full border border-slate-300 shadow-xs inline-block" style="background-color: {{ $color->hex_code ?? '#ff6802' }};"></span>
                                                <span>{{ $color->name }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Size select -->
                            @if($sizes->isNotEmpty())
                                <div>
                                    <span class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">Kích thước</span>
                                    <div class="flex flex-wrap gap-2.5">
                                        @foreach($sizes as $size)
                                            <button type="button" onclick="selectSize({{ $size->id }}, this)" class="size-btn border-2 border-slate-200 dark:border-slate-700 hover:border-orange-500 dark:hover:border-orange-500 rounded-2xl px-4 py-2 text-xs font-bold transition-all bg-white dark:bg-slate-800 shadow-xs">
                                                {{ $size->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Warranties Options -->
                    @if($product->warranties->isNotEmpty())
                        <div class="mb-6">
                            <span class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">Gói bảo hành mở rộng</span>
                            <div class="space-y-2">
                                <label class="flex items-center justify-between p-3 border border-slate-200 dark:border-slate-700 rounded-2xl cursor-pointer select-none text-xs hover:border-orange-500 dark:hover:border-orange-500 transition-colors bg-white dark:bg-slate-800 shadow-xs">
                                    <div class="flex items-center gap-2.5">
                                        <input type="radio" name="warranty_radio" value="" checked onclick="selectWarranty('', 0)" class="w-4 h-4 text-orange-500 focus:ring-orange-500 border-slate-300">
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">Mặc định (Bảo hành hãng tiêu chuẩn)</span>
                                    </div>
                                    <span class="font-bold text-slate-400">Miễn phí</span>
                                </label>
                                @foreach($product->warranties as $warranty)
                                    <label class="flex items-center justify-between p-3 border border-slate-200 dark:border-slate-700 rounded-2xl cursor-pointer select-none text-xs hover:border-orange-500 dark:hover:border-orange-500 transition-colors bg-white dark:bg-slate-800 shadow-xs">
                                        <div class="flex items-center gap-2.5">
                                            <input type="radio" name="warranty_radio" value="{{ $warranty->id }}" onclick="selectWarranty({{ $warranty->id }}, {{ $warranty->price }})" class="w-4 h-4 text-orange-500 focus:ring-orange-500 border-slate-300">
                                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $warranty->name }} ({{ $warranty->duration_months }} tháng)</span>
                                        </div>
                                        <span class="font-bold text-orange-500">+{{ number_format($warranty->price, 0, ',', '.') }} ₫</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Guarantees & Benefits Cards -->
                    <div class="grid grid-cols-2 gap-3 mb-8">
                        <div class="p-3 bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-center gap-3">
                            <div class="p-2 bg-orange-500/10 text-orange-500 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div class="text-left text-xs">
                                <span class="font-bold text-slate-800 dark:text-slate-200 block">Cam kết chính hãng</span>
                                <span class="text-slate-400">100% xuất xứ rõ ràng</span>
                            </div>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-900/40 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-center gap-3">
                            <div class="p-2 bg-blue-500/10 text-blue-500 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </div>
                            <div class="text-left text-xs">
                                <span class="font-bold text-slate-800 dark:text-slate-200 block">1 Đổi 1 trong 30 ngày</span>
                                <span class="text-slate-400">Nếu có lỗi nhà sản xuất</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add to Cart Form -->
                <div>
                    @if($product->stock_quantity > 0)
                        <form action="{{ route('cart.add') }}" method="POST" id="add-to-cart-form" class="space-y-4 ajax-cart-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="product_variant_id" id="form-variant-id" value="">
                            <input type="hidden" name="color_id" id="form-color-id" value="">
                            <input type="hidden" name="size_id" id="form-size-id" value="">
                            <input type="hidden" name="warranty_id" id="form-warranty-id" value="">
                            
                            <div class="flex items-center gap-4">
                                <div class="w-32">
                                    <label class="block text-xs font-bold text-slate-400 mb-1">Số lượng mua</label>
                                    <input type="number" name="quantity" id="quantity-input" value="1" min="1" max="{{ $product->stock_quantity }}"
                                        class="w-full text-center px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-650 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white text-sm font-bold text-slate-800 dark:text-slate-100">
                                </div>
                                
                                <div class="flex-grow pt-5">
                                    <button type="submit" id="add-to-cart-btn" class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-slate-900 font-bold rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Thêm Vào Giỏ Hàng
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <button disabled class="w-full py-3 bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 font-bold rounded-2xl cursor-not-allowed text-center border dark:border-slate-800">
                            Sản Phẩm Tạm Thời Hết Hàng
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- ================= PRODUCT TABS COMPONENT ================= -->
        <div id="product-tabs-wrapper" class="mt-12 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl shadow-sm text-left transition-all duration-300 relative">
            <!-- Tabs Navigation Header Bar (Sticky under main navbar top-16 with Candy Blue #B2D5E5 background) -->
            <div id="sticky-tab-nav" class="sticky top-16 z-30 bg-[#B2D5E5] dark:bg-slate-900 border-b border-[#97C3D7] dark:border-slate-700/80 px-4 sm:px-8 pt-2.5 pb-0 rounded-t-3xl shadow-sm transition-all duration-300">
                <div class="flex items-center justify-between gap-3">
                    <!-- Nav Tabs List -->
                    <div class="flex items-center space-x-1 sm:space-x-2 overflow-x-auto no-scrollbar scroll-smooth py-0.5" role="tablist" aria-label="Chi tiết sản phẩm">
                        <!-- Tab 1: Mô tả chi tiết -->
                        <button type="button"
                                id="tab-btn-description"
                                onclick="switchProductTab('description')"
                                role="tab"
                                aria-selected="true"
                                aria-controls="tab-pane-description"
                                class="product-tab-btn active group inline-flex items-center gap-2 py-2.5 px-4 sm:px-5 font-black text-xs sm:text-sm rounded-t-2xl border-b-2 border-slate-900 dark:border-white text-slate-900 dark:text-white bg-white dark:bg-slate-800 shadow-sm transition-all duration-200 shrink-0">
                            <span class="p-1 rounded-lg bg-orange-500/10 text-orange-600 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </span>
                            <span>Mô Tả Sản Phẩm</span>
                        </button>

                        <!-- Tab 2: Thông số kỹ thuật -->
                        <button type="button"
                                id="tab-btn-specifications"
                                onclick="switchProductTab('specifications')"
                                role="tab"
                                aria-selected="false"
                                aria-controls="tab-pane-specifications"
                                class="product-tab-btn group inline-flex items-center gap-2 py-2.5 px-4 sm:px-5 font-bold text-xs sm:text-sm rounded-t-2xl border-b-2 border-transparent text-slate-800 hover:text-slate-950 dark:text-slate-300 dark:hover:text-white hover:bg-white/40 dark:hover:bg-slate-800/60 transition-all duration-200 shrink-0">
                            <span class="p-1 rounded-lg bg-white/40 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </span>
                            <span>Thông Số Kỹ Thuật</span>
                        </button>

                        <!-- Tab 3: Đánh giá & nhận xét -->
                        <button type="button"
                                id="tab-btn-reviews"
                                onclick="switchProductTab('reviews')"
                                role="tab"
                                aria-selected="false"
                                aria-controls="tab-pane-reviews"
                                class="product-tab-btn group inline-flex items-center gap-2 py-2.5 px-4 sm:px-5 font-bold text-xs sm:text-sm rounded-t-2xl border-b-2 border-transparent text-slate-800 hover:text-slate-950 dark:text-slate-300 dark:hover:text-white hover:bg-white/40 dark:hover:bg-slate-800/60 transition-all duration-200 shrink-0">
                            <span class="p-1 rounded-lg bg-white/40 dark:bg-slate-700/50 text-amber-600 group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </span>
                            <span>Đánh Giá &amp; Nhận Xét</span>
                            <span id="tab-reviews-counter" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/70 dark:bg-amber-950/50 text-amber-800 dark:text-amber-300 border border-amber-300/60">{{ $totalReviews }}</span>
                        </button>
                    </div>

                    <!-- Right Side: Mini Buy Widget (Always visible and sticky) -->
                    <div class="flex items-center gap-3 pb-1 shrink-0">
                        <div id="sticky-pinned-bar" class="flex items-center gap-2 sm:gap-3">
                            <div class="text-right hidden sm:block">
                                @if($product->sale_price !== null && $product->sale_price < $product->price)
                                    <span class="text-[10px] text-slate-600 dark:text-slate-400 block line-through leading-none font-medium">{{ number_format($product->price, 0, ',', '.') }} ₫</span>
                                    <span class="text-xs font-black text-slate-900 dark:text-white leading-tight">{{ number_format($product->sale_price, 0, ',', '.') }} ₫</span>
                                @else
                                    <span class="text-xs font-black text-slate-900 dark:text-white leading-tight">{{ number_format($product->price, 0, ',', '.') }} ₫</span>
                                @endif
                            </div>
                            @if($product->stock_quantity > 0)
                                <button type="button" onclick="document.getElementById('add-to-cart-form').requestSubmit ? document.getElementById('add-to-cart-form').requestSubmit() : document.getElementById('add-to-cart-btn').click()" class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-black rounded-xl shadow-md transition-all flex items-center gap-1.5 hover:scale-105 active:scale-95 shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span>Thêm Giỏ</span>
                                </button>
                            @else
                                <span class="px-3 py-1 bg-slate-200 dark:bg-slate-700 text-slate-500 text-xs font-bold rounded-xl">Hết Hàng</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content Panels Container -->
            <div class="p-6 sm:p-8">
                <!-- ================= TAB PANE 1: MÔ TẢ SẢN PHẨM ================= -->
                <div id="tab-pane-description" role="tabpanel" aria-labelledby="tab-btn-description" class="space-y-6 animate-fadeIn">
                    <div class="prose max-w-none text-sm text-slate-600 dark:text-slate-350 leading-relaxed space-y-4">
                        @if($product->description)
                            {!! $product->description !!}
                        @else
                            <p class="text-slate-400 italic">Chưa có thông tin mô tả chi tiết cho sản phẩm này.</p>
                        @endif
                    </div>
                </div>

                <!-- ================= TAB PANE 2: THÔNG SỐ KỸ THUẬT ================= -->
                <div id="tab-pane-specifications" role="tabpanel" aria-labelledby="tab-btn-specifications" class="hidden space-y-8 animate-fadeIn">
                    <!-- Comprehensive Specifications Table -->
                    <div class="border border-slate-100 dark:border-slate-700 rounded-3xl overflow-hidden shadow-xs">
                        <div class="bg-slate-50 dark:bg-slate-900/60 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <h3 class="font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                Bảng Thông Số Kỹ Thuật Chi Tiết
                            </h3>
                            <span class="text-xs text-slate-400 font-medium">Mã SP: {{ $product->code }}</span>
                        </div>

                        <div class="divide-y divide-slate-100 dark:divide-slate-700/60 text-xs sm:text-sm">
                            <div class="grid grid-cols-1 sm:grid-cols-12 p-4 bg-slate-50/50 dark:bg-slate-800/40">
                                <span class="sm:col-span-4 font-bold text-slate-500 dark:text-slate-400">Thương hiệu</span>
                                <span class="sm:col-span-8 font-semibold text-slate-800 dark:text-slate-100 mt-1 sm:mt-0">{{ $product->brandRelation ? $product->brandRelation->name : ($product->brand ?? 'GiaDụngPro') }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 p-4 bg-white dark:bg-slate-800">
                                <span class="sm:col-span-4 font-bold text-slate-500 dark:text-slate-400">Mã sản phẩm / SKU</span>
                                <span class="sm:col-span-8 font-semibold text-slate-800 dark:text-slate-100 mt-1 sm:mt-0">{{ $product->code }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 p-4 bg-slate-50/50 dark:bg-slate-800/40">
                                <span class="sm:col-span-4 font-bold text-slate-500 dark:text-slate-400">Danh mục</span>
                                <span class="sm:col-span-8 font-semibold text-slate-800 dark:text-slate-100 mt-1 sm:mt-0">{{ $product->category ? $product->category->name : 'Gia dụng gia đình' }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 p-4 bg-white dark:bg-slate-800">
                                <span class="sm:col-span-4 font-bold text-slate-500 dark:text-slate-400">Xuất xứ / Nguồn gốc</span>
                                <span class="sm:col-span-8 font-semibold text-slate-800 dark:text-slate-100 mt-1 sm:mt-0">{{ $product->is_domestic ? 'Hàng nội địa' : 'Hàng ngoại nhập khẩu chính ngạch' }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 p-4 bg-slate-50/50 dark:bg-slate-800/40">
                                <span class="sm:col-span-4 font-bold text-slate-500 dark:text-slate-400">Đơn vị tính</span>
                                <span class="sm:col-span-8 font-semibold text-slate-800 dark:text-slate-100 mt-1 sm:mt-0">{{ $product->unit }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 p-4 bg-white dark:bg-slate-800">
                                <span class="sm:col-span-4 font-bold text-slate-500 dark:text-slate-400">Tình trạng kho</span>
                                <span class="sm:col-span-8 font-semibold {{ $product->stock_quantity > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} mt-1 sm:mt-0">
                                    {{ $product->stock_quantity > 0 ? 'Còn hàng sẵn sàng giao' : 'Tạm thời hết hàng' }}
                                </span>
                            </div>
                            @if($product->warranties->isNotEmpty())
                                <div class="grid grid-cols-1 sm:grid-cols-12 p-4 bg-slate-50/50 dark:bg-slate-800/40">
                                    <span class="sm:col-span-4 font-bold text-slate-500 dark:text-slate-400">Bảo hành tiêu chuẩn</span>
                                    <span class="sm:col-span-8 font-semibold text-emerald-600 dark:text-emerald-400 mt-1 sm:mt-0">
                                        {{ $product->warranties->pluck('name')->join(' | ') }}
                                    </span>
                                </div>
                            @endif

                            @if(!empty($product->specifications) && is_array($product->specifications))
                                @php $specIndex = 0; @endphp
                                @foreach($product->specifications as $specKey => $specVal)
                                    @php $specIndex++; @endphp
                                    <div class="grid grid-cols-1 sm:grid-cols-12 p-4 {{ $specIndex % 2 == 0 ? 'bg-slate-50/50 dark:bg-slate-800/40' : 'bg-white dark:bg-slate-800' }}">
                                        <span class="sm:col-span-4 font-bold text-slate-500 dark:text-slate-400">{{ $specKey }}</span>
                                        <span class="sm:col-span-8 font-semibold text-slate-800 dark:text-slate-100 mt-1 sm:mt-0">{{ $specVal }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ================= TAB PANE 3: ĐÁNH GIÁ & NHẬN XÉT ================= -->
                <div id="tab-pane-reviews" role="tabpanel" aria-labelledby="tab-btn-reviews" class="hidden space-y-8 animate-fadeIn">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start mb-8">
                        <!-- Summary -->
                        <div class="text-center bg-slate-50 dark:bg-slate-900/40 p-6 rounded-2xl border dark:border-slate-700">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Điểm Đánh Giá Trung Bình</span>
                            <span class="text-5xl font-extrabold text-orange-500 block">{{ number_format($avgRating ?? 0, 1) }}</span>
                            <div class="flex justify-center text-amber-400 my-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= round($avgRating ?? 0) ? 'fill-current' : 'text-slate-300 dark:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.252.586 1.813l-3.97 2.87a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.87a1 1 0 00-1.175 0l-3.97 2.87c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.364-1.118l-3.97-2.87c-.77-.56-.375-1.813.587-1.813h4.906a1 1 0 00.95-.69l1.519-4.674z"></path></svg>
                                @endfor
                            </div>
                            <span class="text-xs text-slate-400 block">({{ $totalReviews }} đánh giá đã duyệt)</span>
                        </div>

                        <!-- Star breakdown -->
                        <div class="space-y-2">
                            @foreach($starDistribution as $star => $count)
                                @php
                                    $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                                @endphp
                                <div class="flex items-center text-xs gap-3">
                                    <span class="w-3 text-right font-semibold">{{ $star }}⭐</span>
                                    <div class="flex-grow bg-slate-100 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden">
                                        <div class="bg-orange-500 h-full rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="w-8 text-right text-slate-400">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Review filters -->
                        <div class="bg-slate-50 dark:bg-slate-900/20 p-5 rounded-2xl border dark:border-slate-750">
                            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Lọc nhận xét</span>
                            <form action="{{ route('products.show', $product->slug) }}" method="GET" class="space-y-3 text-xs">
                                <div>
                                    <select name="rating" onchange="this.form.submit()" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                        <option value="">Tất cả số sao</option>
                                        @for($i = 5; $i >= 1; $i--)
                                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} sao</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center cursor-pointer select-none">
                                        <input type="checkbox" name="has_image" value="1" {{ request('has_image') ? 'checked' : '' }} onchange="this.form.submit()" class="w-4 h-4 rounded text-orange-500 mr-2 border-slate-350">
                                        Chỉ hiện đánh giá có ảnh
                                    </label>
                                </div>
                                <div>
                                    <select name="review_sort" onchange="this.form.submit()" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-orange-500">
                                        <option value="latest" {{ request('review_sort') === 'latest' ? 'selected' : '' }}>Nhận xét mới nhất</option>
                                        <option value="helpful" {{ request('review_sort') === 'helpful' ? 'selected' : '' }}>Hữu ích nhất</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Review submission form (authenticated purchasers only) -->
                    @auth
                        @if($hasPurchased)
                            <div class="mb-10 bg-slate-50 dark:bg-slate-900/30 p-6 rounded-2xl border dark:border-slate-700">
                                <h3 class="font-bold text-sm text-slate-800 dark:text-slate-200 mb-3 flex items-center gap-1.5">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Viết đánh giá sản phẩm của bạn
                                </h3>
                                <form action="{{ route('products.reviews.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Mức độ hài lòng</label>
                                            <div class="flex items-center gap-2">
                                                @for($star = 1; $star <= 5; $star++)
                                                    <label class="cursor-pointer">
                                                        <input type="radio" name="rating" value="{{ $star }}" required class="sr-only peer">
                                                        <span class="text-2xl text-slate-350 dark:text-slate-600 peer-checked:text-amber-400 hover:text-amber-300">★</span>
                                                    </label>
                                                @endfor
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Nội dung nhận xét</label>
                                            <textarea name="comment" rows="3" required placeholder="Chia sẻ trải nghiệm sử dụng sản phẩm..." class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-500 text-slate-800 dark:text-slate-100"></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Ảnh thực tế sản phẩm (Không bắt buộc)</label>
                                            <input type="file" name="image" class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 file:cursor-pointer hover:file:bg-orange-100">
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl text-xs font-bold shadow">Gửi đánh giá</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endauth

                    <!-- Reviews list -->
                    <div class="space-y-6">
                        @forelse($reviews as $rev)
                            <div class="border-b border-slate-100 dark:border-slate-700 pb-6 last:border-none">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <span class="font-bold text-xs block text-slate-800 dark:text-slate-200">{{ $rev->user ? $rev->user->name : 'Khách hàng' }}</span>
                                        <span class="text-amber-400 text-[10px]">
                                            @for($i = 1; $i <= 5; $i++)
                                                {{ $i <= $rev->rating ? '★' : '☆' }}
                                            @endfor
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-slate-400">{{ $rev->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-slate-600 dark:text-slate-350 text-xs mt-2 italic">"{{ $rev->comment }}"</p>
                                
                                <!-- Review Photo -->
                                @if($rev->image)
                                    <div class="mt-3 max-w-[120px] rounded-lg overflow-hidden border dark:border-slate-700">
                                        <img src="{{ $rev->image_url }}" class="w-full object-cover cursor-pointer hover:scale-105 transition-transform" onclick="window.open(this.src)">
                                    </div>
                                @endif

                                <!-- Helpful/Upvote Action -->
                                <div class="flex items-center gap-4 mt-3">
                                    @auth
                                        <button onclick="upvoteReview({{ $rev->id }}, this)" class="flex items-center gap-1 text-[10px] text-slate-400 hover:text-orange-500 transition-colors focus:outline-none">
                                            @php
                                                $voted = auth()->user()->id ? \App\Models\ReviewVote::where('user_id', auth()->user()->id)->where('review_id', $rev->id)->exists() : false;
                                            @endphp
                                            <svg class="w-3.5 h-3.5 {{ $voted ? 'fill-orange-500 text-orange-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2m0 10V10"></path></svg>
                                            Hữu ích (<span class="upvote-count">{{ $rev->votes()->count() }}</span>)
                                        </button>
                                    @else
                                        <span class="text-[10px] text-slate-400 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2m0 10V10"></path></svg>
                                            Hữu ích ({{ $rev->votes()->count() }})
                                        </span>
                                    @endauth
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-slate-400 text-xs">
                                <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                </div>
                                <span class="font-medium block text-slate-600 dark:text-slate-350">Chưa có đánh giá nào cho sản phẩm này.</span>
                                <span class="text-[11px] text-slate-400 block mt-1">Hãy là người đầu tiên mua và trải nghiệm để lại nhận xét nhé!</span>
                            </div>
                        @endforelse

                        <!-- Paginate links -->
                        <div class="pt-4">
                            {{ $reviews->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->isNotEmpty())
            <div class="mt-16 text-left">
                <h2 class="text-2xl font-extrabold text-slate-950 dark:text-white mb-8">Sản Phẩm Liên Quan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-8">
                    @foreach($relatedProducts as $relProduct)
                        @include('partials.product-card', ['product' => $relProduct])
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Gallery Script -->
    <script>
        function changeMainImage(src) {
            const mainImg = document.getElementById('main-image');
            if (mainImg) {
                mainImg.classList.add('opacity-0');
                setTimeout(() => {
                    mainImg.src = src;
                    mainImg.classList.remove('opacity-0');
                }, 150);
            }
        }

        // Selected attributes
        let selectedColorId = null;
        let selectedSizeId = null;

        function selectColor(id, btn) {
            // Remove active classes
            document.querySelectorAll('.color-btn').forEach(b => {
                b.classList.remove('border-orange-500', 'text-orange-500');
            });
            btn.classList.add('border-orange-500', 'text-orange-500');
            selectedColorId = id;
            document.getElementById('form-color-id').value = id;
            checkVariant();
        }

        function selectSize(id, btn) {
            // Remove active classes
            document.querySelectorAll('.size-btn').forEach(b => {
                b.classList.remove('border-orange-500', 'text-orange-500');
            });
            btn.classList.add('border-orange-500', 'text-orange-500');
            selectedSizeId = id;
            document.getElementById('form-size-id').value = id;
            checkVariant();
        }

        function selectWarranty(id, price) {
            document.getElementById('form-warranty-id').value = id;
        }

        function checkVariant() {
            // If both attributes are selected or only colors exist or only sizes exist, fetch variant price/stock
            const hasColors = {{ $colors->isNotEmpty() ? 'true' : 'false' }};
            const hasSizes = {{ $sizes->isNotEmpty() ? 'true' : 'false' }};

            if ((hasColors && !selectedColorId) || (hasSizes && !selectedSizeId)) {
                return; // Wait for selection
            }

            fetch(`/products/{{ $product->id }}/variant-info?color_id=${selectedColorId || ''}&size_id=${selectedSizeId || ''}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update SKU
                        document.getElementById('product-sku').innerText = data.sku;
                        // Update Pricing
                        const activePriceEl = document.getElementById('active-price');
                        const originalPriceEl = document.getElementById('original-price');

                        if (data.has_sale) {
                            activePriceEl.innerText = data.sale_price;
                            if (originalPriceEl) {
                                originalPriceEl.innerText = data.price;
                            }
                        } else {
                            activePriceEl.innerText = data.price;
                            if (originalPriceEl) {
                                originalPriceEl.innerText = '';
                            }
                        }

                        // Update Stock
                        const stockCountEl = document.getElementById('stock-count');
                        if (stockCountEl) {
                            stockCountEl.innerText = data.stock_quantity;
                        }

                        const stockBadge = document.getElementById('stock-badge');
                        const quantityInput = document.getElementById('quantity-input');

                        if (data.stock_quantity > 0) {
                            stockBadge.className = 'text-xs font-semibold px-2.5 py-1 bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 rounded';
                            stockBadge.innerHTML = `Còn hàng (Tồn: <span id="stock-count">${data.stock_quantity}</span>)`;
                            if (quantityInput) {
                                quantityInput.max = data.stock_quantity;
                                quantityInput.disabled = false;
                            }
                            document.getElementById('add-to-cart-btn').disabled = false;
                        } else {
                            stockBadge.className = 'text-xs font-semibold px-2.5 py-1 bg-red-50 text-red-700 border border-red-200 rounded';
                            stockBadge.innerHTML = 'Hết hàng';
                            if (quantityInput) {
                                quantityInput.disabled = true;
                            }
                            document.getElementById('add-to-cart-btn').disabled = true;
                        }

                        // Update Image
                        if (data.thumbnail) {
                            changeMainImage(data.thumbnail);
                        }

                        // Update Form Hidden ID
                        document.getElementById('form-variant-id').value = data.variant_id;
                    }
                });
        }

        // Toggle Wishlist via AJAX
        function toggleWishlist(productId) {
            fetch(`/products/${productId}/wishlist`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const btn = document.getElementById('wishlist-btn');
                    const svg = btn.querySelector('svg');
                    if (data.saved) {
                        svg.classList.add('fill-red-500', 'text-red-500');
                    } else {
                        svg.classList.remove('fill-red-500', 'text-red-500');
                    }
                    alert(data.message);
                }
            });
        }

        // Upvote Review via AJAX
        function upvoteReview(reviewId, btn) {
            fetch(`/products/reviews/${reviewId}/vote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const countEl = btn.querySelector('.upvote-count');
                    const svg = btn.querySelector('svg');
                    countEl.innerText = data.votes_count;
                    if (data.voted) {
                        svg.classList.add('fill-orange-500', 'text-orange-500');
                    } else {
                        svg.classList.remove('fill-orange-500', 'text-orange-500');
                    }
                }
            });
        }

        // ================= PRODUCT TABS SWITCHER =================
        function switchProductTab(tabId, scrollIntoView = false) {
            const tabs = ['description', 'specifications', 'reviews'];
            
            tabs.forEach(id => {
                const btn = document.getElementById(`tab-btn-${id}`);
                const pane = document.getElementById(`tab-pane-${id}`);
                if (!btn || !pane) return;

                if (id === tabId) {
                    btn.classList.add('active', 'border-slate-900', 'dark:border-white', 'text-slate-900', 'dark:text-white', 'bg-white', 'dark:bg-slate-800', 'shadow-sm');
                    btn.classList.remove('border-transparent', 'text-slate-800', 'dark:text-slate-300', 'hover:bg-white/40');
                    btn.setAttribute('aria-selected', 'true');
                    pane.classList.remove('hidden');
                } else {
                    btn.classList.remove('active', 'border-slate-900', 'dark:border-white', 'text-slate-900', 'dark:text-white', 'bg-white', 'dark:bg-slate-800', 'shadow-sm');
                    btn.classList.add('border-transparent', 'text-slate-800', 'dark:text-slate-300', 'hover:bg-white/40');
                    btn.setAttribute('aria-selected', 'false');
                    pane.classList.add('hidden');
                }
            });

            // Smooth scroll into view if user is scrolled past or requested
            const wrapper = document.getElementById('product-tabs-wrapper');
            if (wrapper) {
                const navHeight = 64; // height of main header
                const targetOffset = wrapper.getBoundingClientRect().top + window.pageYOffset - navHeight;
                if (scrollIntoView || window.pageYOffset > (targetOffset + 15)) {
                    window.scrollTo({
                        top: targetOffset,
                        behavior: 'smooth'
                    });
                }
            }
        }

        // ================= STICKY TAB NAV SCROLL LISTENER =================
        function handleStickyTabNav() {
            const stickyNav = document.getElementById('sticky-tab-nav');
            const wrapper = document.getElementById('product-tabs-wrapper');
            if (!stickyNav || !wrapper) return;

            const rect = wrapper.getBoundingClientRect();
            const isSticking = rect.top <= 66 && rect.bottom > 160;

            if (isSticking) {
                stickyNav.classList.add('shadow-lg', 'rounded-b-2xl', 'border-b-2', 'border-[#8bbcd2]');
            } else {
                stickyNav.classList.remove('shadow-lg', 'rounded-b-2xl', 'border-b-2', 'border-[#8bbcd2]');
            }
        }
        window.addEventListener('scroll', handleStickyTabNav, { passive: true });
        window.addEventListener('resize', handleStickyTabNav, { passive: true });

        // Handle initial Hash in URL (e.g. #reviews, #specs, #description)
        document.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash.toLowerCase();
            if (hash === '#reviews' || hash === '#danh-gia') {
                switchProductTab('reviews', false);
            } else if (hash === '#specifications' || hash === '#specs' || hash === '#thong-so') {
                switchProductTab('specifications', false);
            } else if (hash === '#description' || hash === '#mo-ta') {
                switchProductTab('description', false);
            }
            handleStickyTabNav();
        });
    </script>
@endsection
