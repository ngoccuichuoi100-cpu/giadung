@if($products->isEmpty())
    <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl p-16 text-center shadow-sm">
        <svg class="w-20 h-20 text-slate-300 dark:text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="font-extrabold text-slate-800 dark:text-slate-200 text-xl mb-1">Không tìm thấy sản phẩm phù hợp</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">Vui lòng thử lại với từ khóa khác hoặc xóa bộ lọc để xem toàn bộ danh mục sản phẩm.</p>
        <a href="{{ route('products.index') }}" class="inline-block mt-6 px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-slate-950 font-extrabold text-xs rounded-full shadow-lg transition-all">
            Xem Tất Cả Sản Phẩm
        </a>
    </div>
@else
    <!-- Total count header indicator -->
    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 px-1">
        <span>Tìm thấy <strong class="text-orange-500 font-extrabold">{{ $products->total() }}</strong> sản phẩm phù hợp</span>
    </div>

    <!-- Product Grid (3-4 Columns with 3D Tilt Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @foreach($products as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>

    <!-- Pagination Bar with ajax-pagination class -->
    <div class="pt-8 border-t border-slate-100 dark:border-slate-700 flex justify-center ajax-pagination">
        {{ $products->links() }}
    </div>
@endif
