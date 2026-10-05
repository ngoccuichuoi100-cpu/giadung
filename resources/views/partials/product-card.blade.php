<div class="product-card-3d group bg-white dark:bg-slate-800 rounded-3xl border hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between overflow-hidden relative cursor-pointer"
     onclick="window.location.href='{{ route('products.show', $product->slug) }}'">
    
    <!-- Discount Badge -->
    @if($product->sale_price !== null && $product->sale_price < $product->price)
        <span class="absolute top-4 left-4 bg-orange-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow z-10">
            Giảm {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
        </span>
    @endif

    <!-- Product Image -->
    <a href="{{ route('products.show', $product->slug) }}" class="block aspect-square w-full bg-slate-50 overflow-hidden relative">
        @if($product->thumbnail)
            <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <!-- Dummy Fallback SVG Image -->
            <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif

        @if($product->stock_quantity <= 0)
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center">
                <span class="bg-slate-900/80 text-white text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider">Hết Hàng</span>
            </div>
        @endif
    </a>

    <!-- Info Content -->
    <div class="p-5 flex-grow flex flex-col justify-between">
        <div>
            <!-- Brand -->
            @if($product->brand)
                <span class="text-xs font-semibold text-slate-400 tracking-wider uppercase block mb-1">{{ $product->brand }}</span>
            @else
                <span class="text-xs font-semibold text-slate-400 tracking-wider uppercase block mb-1">GiaDụngPro</span>
            @endif

            <!-- Title -->
            <h3 class="font-extrabold text-slate-800 dark:text-white dark:group-hover:bg-gradient-to-r dark:group-hover:from-white dark:group-hover:via-cyan-100 dark:group-hover:to-candy-blue dark:group-hover:bg-clip-text dark:group-hover:text-transparent text-sm group-hover:text-orange-500 transition-all line-clamp-2 h-10 mb-2">
                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
            </h3>
        </div>

        <div>
            <!-- Price -->
            <div class="flex items-baseline gap-2 mb-4">
                @if($product->sale_price !== null && $product->sale_price < $product->price)
                    <span class="text-base font-extrabold text-[#0284c7] dark:text-[#38bdf8]">{{ number_format($product->sale_price, 0, ',', '.') }} ₫</span>
                    <span class="text-xs text-slate-400 line-through">{{ number_format($product->price, 0, ',', '.') }} ₫</span>
                @else
                    <span class="text-base font-extrabold text-slate-900 dark:text-white">{{ number_format($product->price, 0, ',', '.') }} ₫</span>
                @endif
            </div>

            <!-- Action Button -->
            @if($product->stock_quantity > 0)
                @php
                    $firstVariant = $product->variants ? $product->variants->where('status', 'active')->first() : null;
                @endphp
                <form action="{{ route('cart.add') }}" method="POST" class="ajax-cart-form" onclick="event.stopPropagation()">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    @if($firstVariant)
                        <input type="hidden" name="product_variant_id" value="{{ $firstVariant->id }}">
                        <input type="hidden" name="color_id" value="{{ $firstVariant->color_id }}">
                        <input type="hidden" name="size_id" value="{{ $firstVariant->size_id }}">
                    @endif
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full py-2.5 bg-[#B2D5E5] hover:bg-[#9fcbe0] dark:bg-candy-blue dark:hover:bg-[#9fcbe0] text-slate-900 text-xs font-extrabold rounded-xl transition-all duration-300 flex items-center justify-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Thêm vào giỏ
                    </button>
                </form>
            @else
                <button disabled class="w-full py-2.5 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed flex items-center justify-center gap-1.5">
                    Hết hàng
                </button>
            @endif
        </div>
    </div>
</div>
