@extends('layouts.client')

@section('title', 'Danh Sách Sản Phẩm Gia Dụng Cao Cấp - GiaDụngPro')

@section('content')
    <div class="page-entrance-smooth max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">
        
        <!-- SECTION 1: TOP HERO HEADER - CATEGORIES & SMART SEARCH BAR (VỊ TRÍ ĐẦU TIÊN) -->
        <div class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white rounded-3xl p-8 sm:p-10 shadow-md border border-slate-150 dark:border-slate-800 relative overflow-hidden text-left">
            <!-- Background Glow Effects -->
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-8">
                <!-- Title & Header Info -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-150 dark:border-slate-800 pb-6">
                    <div>
                        <span class="px-3 py-1 bg-orange-100 dark:bg-orange-500/20 text-slate-900 dark:text-orange-400 border border-orange-200 dark:border-orange-500/30 rounded-full text-xs font-bold uppercase tracking-widest inline-block mb-2">
                            Mua Sắm Dễ Dàng & Nhanh Chóng
                        </span>
                        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 metallic-dark-text">
                            @if(request('category'))
                                Danh mục: {{ $categories->firstWhere('slug', request('category'))?->name ?? 'Sản phẩm' }}
                            @elseif(request('brand'))
                                Thương hiệu: {{ $brands->firstWhere('slug', request('brand'))?->name ?? 'Sản phẩm' }}
                            @elseif(request('q'))
                                Kết quả cho: "{{ request('q') }}"
                            @else
                                Khám Phá Sản Phẩm Gia Dụng
                            @endif
                        </h1>
                    </div>
                    <div class="text-slate-700 dark:text-slate-300 text-xs sm:text-sm font-medium bg-slate-100 dark:bg-slate-800/80 px-4 py-2 rounded-2xl border border-slate-200 dark:border-slate-700 w-fit">
                        Tìm thấy <span class="text-orange-600 dark:text-orange-400 font-extrabold text-base">{{ $products->total() }}</span> sản phẩm cao cấp
                    </div>
                </div>

                <!-- 1A: SMART LIVE SEARCH BAR -->
                <form id="hero-search-form" action="{{ route('products.index') }}" method="GET" class="relative max-w-3xl search-container-with-history">
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('brand')) <input type="hidden" name="brand" value="{{ request('brand') }}"> @endif

                    <div class="relative flex items-center shadow-lg rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 focus-within:border-orange-500 focus-within:ring-2 focus-within:ring-orange-500/30 transition-all">
                        <div class="pl-5 text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="q" id="products-search-input" value="{{ request('q') }}" placeholder="Nhập tên sản phẩm, mã thương hiệu bạn muốn tìm..."
                            autocomplete="off"
                            class="w-full pl-3 pr-28 py-4 bg-transparent text-slate-900 dark:text-white placeholder-slate-400 text-sm font-medium focus:outline-none">
                        
                        <button type="submit" class="absolute right-2 top-2 bottom-2 px-6 bg-orange-500 hover:bg-orange-600 text-slate-950 font-extrabold text-xs rounded-xl transition-all shadow-lg flex items-center gap-1.5">
                            Tìm Kiếm
                        </button>
                    </div>
                    @include('partials.search-history-dropdown', ['dropdownClass' => 'w-full left-0 right-0 shadow-2xl'])
                </form>

                <!-- QUICK FILTER TOOLBAR & SORT OPTIONS (NẰM DƯỚI THANH TÌM KIẾM) -->
                <div class="bg-slate-50/80 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 rounded-2xl p-5 sm:p-6 shadow-xs text-left">
                    <form id="products-filter-form" action="{{ route('products.index') }}" method="GET" class="space-y-4">
                        <input type="hidden" name="q" id="filter-hidden-q" value="{{ request('q') }}">
                        <input type="hidden" name="category" id="filter-hidden-category" value="{{ request('category') }}">

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <!-- Brand Filter -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Thương hiệu</label>
                                <select name="brand" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-2xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 text-slate-800 dark:text-slate-200">
                                    <option value="">Tất cả thương hiệu</option>
                                    @foreach($brands as $b)
                                        <option value="{{ $b->slug }}" {{ request('brand') === $b->slug ? 'selected' : '' }}>{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Origin Filter -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Xuất xứ sản phẩm</label>
                                <select name="origin" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-2xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 text-slate-800 dark:text-slate-200">
                                    <option value="">Tất cả xuất xứ</option>
                                    <option value="domestic" {{ request('origin') === 'domestic' ? 'selected' : '' }}>Hàng nội địa</option>
                                    <option value="imported" {{ request('origin') === 'imported' ? 'selected' : '' }}>Hàng ngoại nhập</option>
                                </select>
                            </div>

                            <!-- Sort Option -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Sắp xếp theo</label>
                                <select name="sort" class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-2xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 text-slate-800 dark:text-slate-200">
                                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Mới nhất</option>
                                    <option value="best_sellers" {{ request('sort') === 'best_sellers' ? 'selected' : '' }}>Bán chạy nhất</option>
                                    <option value="top_rated" {{ request('sort') === 'top_rated' ? 'selected' : '' }}>Đánh giá cao nhất</option>
                                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-center pt-2 border-t border-slate-200/80 dark:border-slate-700/60">
                            <!-- Price Min -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Giá từ (₫)</label>
                                <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="0" min="0" step="10000"
                                    class="w-full px-3.5 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-2xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 text-slate-800 dark:text-slate-200">
                            </div>

                            <!-- Price Max -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Đến giá (₫)</label>
                                <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Tối đa" min="0" step="10000"
                                    class="w-full px-3.5 py-2 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-2xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 text-slate-800 dark:text-slate-200">
                            </div>

                            <!-- In stock filter -->
                            <div class="pt-2 sm:pt-4">
                                <label class="inline-flex items-center gap-2 cursor-pointer select-none text-xs font-bold text-slate-700 dark:text-slate-300">
                                    <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}
                                        class="w-4 h-4 rounded text-orange-500 focus:ring-orange-500 border-slate-300 dark:border-slate-600">
                                    <span>Chỉ hiển thị còn hàng</span>
                                </label>
                            </div>

                            <!-- Action buttons -->
                            <div class="flex gap-2 pt-2 sm:pt-4">
                                <button type="submit" class="flex-1 py-2.5 bg-orange-500 hover:bg-orange-600 text-slate-950 font-extrabold text-xs rounded-2xl transition-all shadow">
                                    Lọc kết quả
                                </button>
                                <a href="{{ route('products.index', array_filter(['category' => request('category'), 'q' => request('q')])) }}" class="py-2.5 px-4 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-2xl transition-all text-center">
                                    Đặt lại
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 1B: CATEGORY FILTER BADGES / PILLS BAR (DANH MỤC TRÊN CÙNG) -->
                <div>
                    <h3 class="text-xs font-bold text-slate-700 dark:text-slate-400 uppercase tracking-wider mb-3">Danh Mục Sản Phẩm Nổi Bật:</h3>
                    <div id="category-pills-container" class="flex flex-wrap items-center gap-2.5">
                        <!-- All Categories Pill -->
                        <a href="{{ route('products.index', array_filter(request()->except('category', 'page'))) }}"
                           data-category=""
                           class="category-filter-pill px-4 py-2 rounded-2xl text-xs font-bold transition-all border flex items-center gap-2 cursor-pointer {{ !request('category') ? 'active bg-orange-500 text-slate-950 border-orange-500 shadow-lg shadow-orange-500/20 scale-105' : 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            Tất cả danh mục
                        </a>

                        @foreach($categories as $cat)
                            @php
                                $isActive = request('category') === $cat->slug;
                            @endphp
                            <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                               data-category="{{ $cat->slug }}"
                               class="category-filter-pill px-4 py-2 rounded-2xl text-xs font-bold transition-all border flex items-center gap-2 cursor-pointer {{ $isActive ? 'active bg-orange-500 text-slate-950 border-orange-500 shadow-lg shadow-orange-500/20 scale-105' : 'bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                                <span>{{ $cat->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: PRODUCTS GRID DISPLAY AREA (REALTIME AJAX REFRESH) -->
        <div id="products-grid-container" class="space-y-8 relative min-h-[350px]">
            <!-- Loading Indicator Overlay -->
            <div id="products-loading-overlay" class="hidden absolute inset-0 bg-white/70 dark:bg-slate-900/70 backdrop-blur-xs rounded-3xl z-20 flex flex-col items-center justify-center gap-3 transition-opacity">
                <div class="w-10 h-10 border-3 border-orange-500 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Đang lọc sản phẩm...</span>
            </div>

            <div id="products-grid-content" class="space-y-8">
                @include('products.partials.grid', ['products' => $products])
            </div>
        </div>

        <!-- SECTION 3: RECOMMENDED PRODUCTS BASED ON SEARCH HISTORY -->
        @if(isset($recommendedProducts) && $recommendedProducts->isNotEmpty())
            <div class="bg-gradient-to-br from-white to-orange-50/30 dark:from-slate-900 dark:to-slate-800/80 rounded-3xl p-6 sm:p-8 border border-orange-200/60 dark:border-slate-700/80 shadow-sm relative overflow-hidden text-left mt-10">
                <!-- Background Glow -->
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200/60 dark:border-slate-700 pb-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-100 dark:bg-orange-500/20 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-500/30 flex items-center gap-1.5 shadow-xs">
                                    <span>✨</span> Gợi ý dành riêng cho bạn
                                </span>
                                @if($hasSearchHistory && !empty($recommendedKeywords))
                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                        Dựa trên tìm kiếm gần đây:
                                        @foreach(array_slice($recommendedKeywords, 0, 3) as $kw)
                                            <span class="text-orange-600 dark:text-orange-400 font-semibold">"{{ $kw }}"</span>{{ !$loop->last ? ',' : '' }}
                                        @endforeach
                                    </span>
                                @endif
                            </div>
                            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                @if($hasSearchHistory)
                                    Sản Phẩm Đề Xuất Phù Hợp Với Bạn
                                @else
                                    Sản Phẩm Gia Dụng Được Yêu Thích Nhất
                                @endif
                            </h2>
                        </div>
                        <a href="{{ route('products.index', ['sort' => 'best_sellers']) }}" class="text-xs font-bold text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 flex items-center gap-1 transition-colors group">
                            <span>Xem thêm sản phẩm hot</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- Recommended Product Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($recommendedProducts as $recommendedProduct)
                            @include('partials.product-card', ['product' => $recommendedProduct])
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('products-search-input');
    const searchForm = searchInput ? searchInput.closest('form') : null;
    const navbarSearchForm = document.getElementById('navbar-search-form');
    const navbarSearchInput = document.getElementById('navbar-search-input');
    const heroSearchForm = document.getElementById('hero-search-form') || searchForm;
    const filterForm = document.getElementById('products-filter-form');
    const gridContainer = document.getElementById('products-grid-container');
    const gridContent = document.getElementById('products-grid-content');
    const loadingOverlay = document.getElementById('products-loading-overlay');
    const hiddenQ = document.getElementById('filter-hidden-q');
    const hiddenCategory = document.getElementById('filter-hidden-category');
    const categoryPills = document.querySelectorAll('.category-filter-pill');

    if (!filterForm || !gridContent) return;

    let filterDebounceTimer = null;
    let abortController = null;

    // Execute AJAX Filter
    const executeFilter = (pageUrl = null) => {
        // Show loading overlay
        if (loadingOverlay) loadingOverlay.classList.remove('hidden');

        // Cancel previous pending request
        if (abortController) {
            abortController.abort();
        }
        abortController = new AbortController();

        // Build target URL
        let targetUrl = pageUrl;
        if (!targetUrl) {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams();

            // Sync keyword from search bar
            if (searchInput) {
                const qVal = searchInput.value.trim();
                if (qVal) params.set('q', qVal);
                if (hiddenQ) hiddenQ.value = qVal;
            }

            for (const [key, value] of formData.entries()) {
                if (value && key !== 'q') {
                    params.set(key, value);
                }
            }

            targetUrl = `{{ route('products.index') }}?` + params.toString();
        }

        // Update browser URL history without page reload
        window.history.replaceState({ path: targetUrl }, '', targetUrl);

        fetch(targetUrl, {
            signal: abortController.signal,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.html !== undefined) {
                gridContent.innerHTML = data.html;
                bindPaginationEvents();

                // Smooth scroll to top of product grid when paginating
                if (pageUrl && gridContainer) {
                    gridContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                console.error('Filter fetch error:', err);
            }
        })
        .finally(() => {
            if (loadingOverlay) loadingOverlay.classList.add('hidden');
        });
    };

    // Debounced filter trigger
    const debouncedFilter = (delay = 250) => {
        clearTimeout(filterDebounceTimer);
        filterDebounceTimer = setTimeout(() => {
            executeFilter();
        }, delay);
    };

    // 1. Realtime typing in search bar (Debounced 300ms) with bidirectional sync
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            if (navbarSearchInput) navbarSearchInput.value = searchInput.value;
            debouncedFilter(300);
        });
    }

    if (searchForm) {
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const kw = searchInput ? searchInput.value.trim() : '';
            if (window.saveSearchKeyword && kw) window.saveSearchKeyword(kw);
            executeFilter();
        });
    }

    // Sync navbar search input with hero search and trigger live filter
    if (navbarSearchInput) {
        navbarSearchInput.addEventListener('input', () => {
            if (searchInput) searchInput.value = navbarSearchInput.value;
            debouncedFilter(300);
        });
    }

    if (navbarSearchForm) {
        navbarSearchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            if (searchInput && navbarSearchInput) searchInput.value = navbarSearchInput.value;
            const kw = navbarSearchInput ? navbarSearchInput.value.trim() : '';
            if (window.saveSearchKeyword && kw) window.saveSearchKeyword(kw);
            executeFilter();
        });
    }

    // Smart Dynamic Navbar Search: Reveal on scroll past hero search bar, hide when at top
    if (navbarSearchForm && heroSearchForm) {
        let isNavbarSearchVisible = false;

        const updateNavbarSearchVisibility = () => {
            const rect = heroSearchForm.getBoundingClientRect();
            // When hero search bar bottom passes above the sticky header (height ~64px)
            const shouldShow = rect.bottom < 64;

            if (shouldShow !== isNavbarSearchVisible) {
                isNavbarSearchVisible = shouldShow;
                if (shouldShow) {
                    navbarSearchForm.classList.remove('navbar-search-smart-collapsed');
                    navbarSearchForm.classList.add('navbar-search-smart-expanded');
                } else {
                    navbarSearchForm.classList.add('navbar-search-smart-collapsed');
                    navbarSearchForm.classList.remove('navbar-search-smart-expanded');
                }
            }
        };

        window.addEventListener('scroll', updateNavbarSearchVisibility, { passive: true });
        window.addEventListener('resize', updateNavbarSearchVisibility, { passive: true });
        updateNavbarSearchVisibility(); // Check immediately on page load
    }

    // 2. Realtime change on Filter dropdowns and checkboxes
    filterForm.querySelectorAll('select, input[type="checkbox"]').forEach(input => {
        input.addEventListener('change', () => {
            executeFilter();
        });
    });

    // Price range inputs (Debounced 400ms)
    filterForm.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', () => {
            debouncedFilter(400);
        });
    });

    // 3. Category Pills Click Handler
    categoryPills.forEach(pill => {
        pill.addEventListener('click', (e) => {
            e.preventDefault();
            const catSlug = pill.dataset.category || '';

            // Update hidden category input
            if (hiddenCategory) hiddenCategory.value = catSlug;

            // Update active pill UI styling
            categoryPills.forEach(p => {
                p.classList.remove('active', 'bg-orange-500', 'text-slate-950', 'border-orange-500', 'shadow-lg', 'shadow-orange-500/20', 'scale-105');
                p.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-800', 'dark:text-slate-300', 'border-slate-200', 'dark:border-slate-700');
            });
            pill.classList.add('active', 'bg-orange-500', 'text-slate-950', 'border-orange-500', 'shadow-lg', 'shadow-orange-500/20', 'scale-105');
            pill.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-800', 'dark:text-slate-300', 'border-slate-200', 'dark:border-slate-700');

            executeFilter();
        });
    });

    // 4. Reset Button Handler
    const resetBtn = filterForm.querySelector('a[href*="products"]');
    if (resetBtn) {
        resetBtn.addEventListener('click', (e) => {
            e.preventDefault();
            filterForm.reset();
            if (searchInput) searchInput.value = '';
            if (navbarSearchInput) navbarSearchInput.value = '';
            if (hiddenQ) hiddenQ.value = '';
            if (hiddenCategory) hiddenCategory.value = '';

            // Reset category pills to "All"
            categoryPills.forEach(p => {
                if (p.dataset.category === '') {
                    p.classList.add('active', 'bg-orange-500', 'text-slate-950', 'border-orange-500', 'shadow-lg', 'shadow-orange-500/20', 'scale-105');
                    p.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-800', 'dark:text-slate-300', 'border-slate-200', 'dark:border-slate-700');
                } else {
                    p.classList.remove('active', 'bg-orange-500', 'text-slate-950', 'border-orange-500', 'shadow-lg', 'shadow-orange-500/20', 'scale-105');
                    p.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-800', 'dark:text-slate-300', 'border-slate-200', 'dark:border-slate-700');
                }
            });

            executeFilter(`{{ route('products.index') }}`);
        });
    }

    // 5. Intercept filter form submit
    filterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        executeFilter();
    });

    // 6. Bind AJAX pagination click events
    function bindPaginationEvents() {
        const paginationLinks = gridContent.querySelectorAll('.ajax-pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const href = link.getAttribute('href');
                if (href) {
                    executeFilter(href);
                }
            });
        });
    }

    bindPaginationEvents();
});
</script>
@endpush
