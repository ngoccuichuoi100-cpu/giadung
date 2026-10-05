{{-- Search History & Suggestions Dropdown Partial --}}
<div class="search-history-dropdown hidden absolute top-full mt-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-2xl shadow-2xl p-4 z-50 transition-all duration-200 select-none {{ $dropdownClass ?? 'w-72 sm:w-80 md:w-96 left-0' }}">
    
    <!-- Recent Searches Section -->
    <div class="recent-searches-section">
        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100 dark:border-slate-800">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Lịch sử tìm kiếm
            </span>
            <button type="button" class="clear-all-history-btn text-[11px] font-semibold text-slate-400 hover:text-red-500 transition-colors">
                Xóa tất cả
            </button>
        </div>

        <!-- List of recent searches -->
        <div class="recent-searches-list flex flex-col gap-1 pt-2 max-h-52 overflow-y-auto">
            <!-- Items injected by JS -->
            <div class="history-loading-placeholder py-3 text-center text-xs text-slate-400 flex items-center justify-center gap-2">
                <svg class="w-3.5 h-3.5 animate-spin text-orange-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Đang tải lịch sử...</span>
            </div>
        </div>
    </div>

    <!-- Trending / Popular Suggestions Section -->
    <div class="trending-searches-section pt-3 mt-3 border-t border-slate-100 dark:border-slate-800">
        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mb-2.5">
            <span class="text-xs">🔥</span>
            Gợi ý tìm kiếm phổ biến
        </span>
        <div class="trending-searches-list flex flex-wrap gap-1.5">
            <!-- Trending items injected by JS -->
        </div>
    </div>

    <!-- Realtime Live Search Results Section (Shown when user is typing) -->
    <div class="live-search-results-section hidden">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
            <span class="text-[11px] font-bold uppercase tracking-wider text-orange-600 dark:text-orange-400 flex items-center gap-1.5">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                </span>
                Sản phẩm gợi ý (<span class="live-search-count font-extrabold text-slate-800 dark:text-white">0</span>)
            </span>
            <span class="text-[10px] text-slate-400">Kết quả tức thì</span>
        </div>

        <!-- Products List -->
        <div class="live-search-products-list flex flex-col gap-1.5 pt-2 max-h-72 overflow-y-auto">
            <!-- Injected by JS -->
        </div>

        <!-- View all button -->
        <div class="pt-2 mt-2 border-t border-slate-100 dark:border-slate-800 text-center live-search-footer hidden">
            <a href="#" class="live-search-view-all-btn text-xs font-bold text-orange-600 dark:text-orange-400 hover:text-orange-700 hover:underline flex items-center justify-center gap-1">
                <span>Xem tất cả kết quả</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>

</div>
