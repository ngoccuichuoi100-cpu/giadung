/**
 * Search History & Suggestions Handler for GiaDungPro
 * Supports client-side instant localStorage storage, server-side database syncing,
 * auto-saving on typing pause, and suggestion click capture.
 */
document.addEventListener('DOMContentLoaded', () => {
    const LOCAL_STORAGE_KEY = 'giadung_search_history';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Helper: Safely get history array from localStorage
    const getLocalStorageHistory = () => {
        try {
            const raw = localStorage.getItem(LOCAL_STORAGE_KEY);
            if (!raw) return [];
            const parsed = JSON.parse(raw);
            if (!Array.isArray(parsed)) return [];
            return parsed.map(item => {
                if (typeof item === 'string') {
                    return { id: null, keyword: item, time_ago: 'Vừa xong' };
                }
                return {
                    id: item.id || null,
                    keyword: item.keyword || '',
                    time_ago: item.time_ago || 'Vừa xong'
                };
            }).filter(item => item.keyword && item.keyword.trim().length > 0);
        } catch (e) {
            console.error('Error reading localStorage history:', e);
            return [];
        }
    };

    // Helper: Safely save history array to localStorage
    const setLocalStorageHistory = (items) => {
        try {
            localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(items.slice(0, 10)));
        } catch (e) {
            console.error('Error writing to localStorage:', e);
        }
    };

    // Helper: Escape HTML
    const escapeHtml = (str) => {
        if (!str) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };

    // Core Helper: Save keyword to history (localStorage + Server DB)
    const saveKeyword = (keyword, syncToServer = true) => {
        if (!keyword) return;
        const cleanKw = keyword.trim();
        if (cleanKw.length < 2) return;

        let history = getLocalStorageHistory();
        // Remove existing item with identical keyword (case-insensitive)
        history = history.filter(item => item.keyword.toLowerCase() !== cleanKw.toLowerCase());
        // Insert new item at the top
        const newItem = {
            id: null,
            keyword: cleanKw,
            time_ago: 'Vừa xong'
        };
        history.unshift(newItem);
        history = history.slice(0, 10);
        setLocalStorageHistory(history);

        // Notify all search dropdowns across the page
        window.dispatchEvent(new CustomEvent('giadung:search-history-updated', {
            detail: { items: history }
        }));

        // Sync with backend API
        if (syncToServer) {
            fetch('/api/search-history', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ keyword: cleanKw })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.item && data.item.id) {
                    let updated = getLocalStorageHistory();
                    const target = updated.find(i => i.keyword.toLowerCase() === cleanKw.toLowerCase());
                    if (target) {
                        target.id = data.item.id;
                        if (data.item.time_ago) target.time_ago = data.item.time_ago;
                        setLocalStorageHistory(updated);
                    }
                }
            })
            .catch(err => {
                // Silently handle offline/network issues as localStorage already holds the data
            });
        }
    };

    // Expose globally so other scripts (like products.index filter) can call it
    window.saveSearchKeyword = saveKeyword;

    // Helper: Merge server items with localStorage items without wiping out client data
    const mergeServerAndLocal = (serverItems) => {
        const localItems = getLocalStorageHistory();
        const map = new Map();

        // 1. Add server items first (these have real IDs and relative timestamps)
        (serverItems || []).forEach(item => {
            const kw = (item.keyword || '').trim();
            if (kw) {
                map.set(kw.toLowerCase(), {
                    id: item.id || null,
                    keyword: kw,
                    time_ago: item.time_ago || 'Vừa xong'
                });
            }
        });

        // 2. Add local items that do not exist on server yet (preserves client searches)
        localItems.forEach(item => {
            const kw = (item.keyword || '').trim();
            if (kw && !map.has(kw.toLowerCase())) {
                map.set(kw.toLowerCase(), {
                    id: item.id || null,
                    keyword: kw,
                    time_ago: item.time_ago || 'Vừa xong'
                });
                // Sync unsynced item to server in background
                saveKeyword(kw, true);
            }
        });

        const merged = Array.from(map.values()).slice(0, 10);
        setLocalStorageHistory(merged);
        return merged;
    };

    // Initialize all search containers with history dropdown
    const initSearchContainers = () => {
        const containers = document.querySelectorAll('.search-container-with-history');

        containers.forEach(container => {
            const input = container.querySelector('input[name="q"]');
            const dropdown = container.querySelector('.search-history-dropdown');
            const form = container.closest('form') || container.querySelector('form');

            if (!input || !dropdown) return;

            const recentSection = dropdown.querySelector('.recent-searches-section');
            const recentListEl = dropdown.querySelector('.recent-searches-list');
            const trendingSection = dropdown.querySelector('.trending-searches-section');
            const trendingListEl = dropdown.querySelector('.trending-searches-list');
            const clearAllBtn = dropdown.querySelector('.clear-all-history-btn');

            const liveSearchSection = dropdown.querySelector('.live-search-results-section');
            const liveSearchList = dropdown.querySelector('.live-search-products-list');
            const liveSearchCount = dropdown.querySelector('.live-search-count');
            const liveSearchFooter = dropdown.querySelector('.live-search-footer');
            const liveSearchViewAllBtn = dropdown.querySelector('.live-search-view-all-btn');

            let isDataLoaded = false;
            let liveSearchTimeout = null;
            let autoSaveTimeout = null;
            let currentAbortController = null;

            // Render recent list
            const renderRecentList = (items) => {
                if (!recentListEl) return;

                if (!items || items.length === 0) {
                    recentListEl.innerHTML = `
                        <div class="py-2.5 text-center text-xs text-slate-400 dark:text-slate-500 italic">
                            Chưa có lịch sử tìm kiếm gần đây
                        </div>
                    `;
                    if (clearAllBtn) clearAllBtn.style.display = 'none';
                    return;
                }

                if (clearAllBtn) clearAllBtn.style.display = 'inline-block';

                recentListEl.innerHTML = items.map(item => {
                    const kw = typeof item === 'string' ? item : item.keyword;
                    const id = typeof item === 'object' && item.id ? item.id : '';
                    const timeAgo = typeof item === 'object' && item.time_ago ? item.time_ago : '';

                    return `
                        <div class="history-item flex items-center justify-between px-2.5 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors group cursor-pointer" data-keyword="${escapeHtml(kw)}">
                            <div class="flex items-center gap-2 flex-grow min-w-0 pr-2">
                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-orange-500 flex-shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs font-medium text-slate-700 dark:text-slate-200 group-hover:text-orange-600 dark:group-hover:text-orange-400 truncate">
                                    ${escapeHtml(kw)}
                                </span>
                                ${timeAgo ? `<span class="text-[10px] text-slate-400 font-normal ml-auto flex-shrink-0 opacity-70">${timeAgo}</span>` : ''}
                            </div>
                            <button type="button" class="delete-single-history-btn p-1 text-slate-400 hover:text-red-500 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-lg transition-colors flex-shrink-0" data-id="${id}" data-keyword="${escapeHtml(kw)}" title="Xóa">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                }).join('');

                // Attach click to history items (search & promote)
                recentListEl.querySelectorAll('.history-item').forEach(el => {
                    el.addEventListener('click', (e) => {
                        if (e.target.closest('.delete-single-history-btn')) return;
                        const kw = el.dataset.keyword;
                        if (kw) {
                            input.value = kw;
                            saveKeyword(kw);
                            if (form) form.submit();
                        }
                    });
                });

                // Attach click to delete single history item
                recentListEl.querySelectorAll('.delete-single-history-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        e.preventDefault();
                        const id = btn.dataset.id;
                        const kw = btn.dataset.keyword;
                        deleteHistoryItem(id, kw, btn.closest('.history-item'));
                    });
                });
            };

            // Render trending list
            const renderTrendingList = (trending) => {
                if (!trendingListEl || !trending || trending.length === 0) return;

                trendingListEl.innerHTML = trending.map(kw => `
                    <button type="button" class="trending-item-btn px-2.5 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-300 dark:hover:bg-orange-950/40 dark:hover:text-orange-400 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-300 transition-all flex items-center gap-1.5" data-keyword="${escapeHtml(kw)}">
                        <span>${escapeHtml(kw)}</span>
                    </button>
                `).join('');

                trendingListEl.querySelectorAll('.trending-item-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const kw = btn.dataset.keyword;
                        if (kw) {
                            input.value = kw;
                            saveKeyword(kw);
                            if (form) form.submit();
                        }
                    });
                });
            };

            // Fetch from backend API
            const fetchHistoryData = () => {
                fetch('/api/search-history', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        isDataLoaded = true;
                        // Merge server and localStorage without wiping client data
                        const mergedRecent = mergeServerAndLocal(data.recent);
                        renderRecentList(mergedRecent);
                        renderTrendingList(data.trending);
                    }
                })
                .catch(err => {
                    console.error('Failed to fetch search history:', err);
                });
            };

            // Delete single item
            const deleteHistoryItem = (id, keyword, element) => {
                if (element) {
                    element.style.opacity = '0';
                    element.style.transform = 'translateX(10px)';
                    setTimeout(() => {
                        element.remove();
                        const remaining = recentListEl ? recentListEl.querySelectorAll('.history-item') : [];
                        if (remaining.length === 0) {
                            renderRecentList([]);
                        }
                    }, 200);
                }

                // Remove from localStorage
                let cached = getLocalStorageHistory();
                cached = cached.filter(item => item.keyword.toLowerCase() !== keyword.toLowerCase());
                setLocalStorageHistory(cached);

                // Notify other dropdowns on page
                window.dispatchEvent(new CustomEvent('giadung:search-history-updated', {
                    detail: { items: cached }
                }));

                // Send delete request to server (by ID or URL-encoded keyword)
                const deleteTarget = id || encodeURIComponent(keyword);
                fetch(`/api/search-history/${deleteTarget}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).catch(err => console.error('Failed to delete history on server:', err));
            };

            // Clear all history
            if (clearAllBtn) {
                clearAllBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    // Clear localStorage
                    localStorage.removeItem(LOCAL_STORAGE_KEY);
                    renderRecentList([]);

                    // Notify other dropdowns
                    window.dispatchEvent(new CustomEvent('giadung:search-history-updated', {
                        detail: { items: [] }
                    }));

                    // Send clear request to server
                    fetch('/api/search-history', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).catch(err => console.error('Failed to clear history on server:', err));
                });
            }

            // Show dropdown
            const showDropdown = () => {
                dropdown.classList.remove('hidden');
                const kw = input.value.trim();

                if (kw.length === 0) {
                    // Show history, hide live search
                    if (liveSearchSection) liveSearchSection.classList.add('hidden');
                    if (recentSection) recentSection.classList.remove('hidden');
                    if (trendingSection) trendingSection.classList.remove('hidden');

                    // Always render from localStorage immediately
                    renderRecentList(getLocalStorageHistory());

                    if (!isDataLoaded) {
                        fetchHistoryData();
                    }
                } else {
                    executeLiveSearch(kw);
                }
            };

            // Hide dropdown
            const hideDropdown = () => {
                dropdown.classList.add('hidden');
            };

            // Live Search execution
            const executeLiveSearch = (keyword) => {
                const kw = keyword.trim();
                if (!liveSearchSection) return;

                if (kw.length === 0) {
                    liveSearchSection.classList.add('hidden');
                    if (recentSection) recentSection.classList.remove('hidden');
                    if (trendingSection) trendingSection.classList.remove('hidden');
                    renderRecentList(getLocalStorageHistory());
                    return;
                }

                liveSearchSection.classList.remove('hidden');
                if (recentSection) recentSection.classList.add('hidden');
                if (trendingSection) trendingSection.classList.add('hidden');

                if (liveSearchList) {
                    liveSearchList.innerHTML = `
                        <div class="py-4 text-center text-xs text-slate-400 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-orange-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Đang tìm kiếm sản phẩm...</span>
                        </div>
                    `;
                }

                if (currentAbortController) {
                    currentAbortController.abort();
                }
                currentAbortController = new AbortController();

                fetch(`/api/products/live-search?q=${encodeURIComponent(kw)}`, {
                    signal: currentAbortController.signal,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return;

                    if (liveSearchCount) {
                        liveSearchCount.textContent = data.total || 0;
                    }

                    if (!data.products || data.products.length === 0) {
                        liveSearchList.innerHTML = `
                            <div class="py-4 px-2 text-center text-xs text-slate-500 dark:text-slate-400">
                                Không tìm thấy sản phẩm nào chứa "<strong>${escapeHtml(kw)}</strong>"
                            </div>
                        `;
                        if (liveSearchFooter) liveSearchFooter.classList.add('hidden');
                        return;
                    }

                    liveSearchList.innerHTML = data.products.map(p => `
                        <a href="${p.url}" class="live-search-item flex items-center gap-2.5 p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 border border-transparent hover:border-slate-200 dark:border-slate-700 transition-all group">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex-shrink-0 overflow-hidden flex items-center justify-center border border-slate-200 dark:border-slate-700">
                                ${p.thumbnail ? `<img src="${p.thumbnail}" alt="${escapeHtml(p.name)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">` : `<span class="text-xs">📦</span>`}
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="text-[10px] text-slate-400 uppercase font-semibold tracking-wider truncate">${escapeHtml(p.brand || p.category || 'GiaDungPro')}</div>
                                <div class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-orange-500 truncate transition-colors">${escapeHtml(p.name)}</div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-xs font-extrabold text-orange-600 dark:text-orange-400">${p.sale_price_formatted || p.price_formatted}</span>
                                    ${p.sale_price_formatted ? `<span class="text-[10px] text-slate-400 line-through">${p.price_formatted}</span>` : ''}
                                    ${p.discount_percent > 0 ? `<span class="text-[9px] bg-red-500 text-white font-bold px-1 rounded">-${p.discount_percent}%</span>` : ''}
                                </div>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-orange-500 group-hover:translate-x-0.5 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    `).join('');

                    // Save search keyword whenever user clicks on an instant search item
                    liveSearchList.querySelectorAll('.live-search-item').forEach(link => {
                        link.addEventListener('click', () => {
                            saveKeyword(input.value.trim());
                        });
                    });

                    if (liveSearchFooter && liveSearchViewAllBtn) {
                        liveSearchViewAllBtn.href = data.view_all_url || `/products?q=${encodeURIComponent(kw)}`;
                        liveSearchFooter.classList.remove('hidden');
                        liveSearchViewAllBtn.onclick = () => {
                            saveKeyword(input.value.trim());
                        };
                    }
                })
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        console.error('Live search error:', err);
                    }
                });
            };

            // Input typing listener with debounced live search + auto-saving keyword
            input.addEventListener('input', () => {
                showDropdown();

                // Debounce live search
                clearTimeout(liveSearchTimeout);
                liveSearchTimeout = setTimeout(() => {
                    executeLiveSearch(input.value);
                }, 200);

                // Auto-save keyword if user pauses typing for 1000ms with >= 2 characters
                clearTimeout(autoSaveTimeout);
                const currentKw = input.value.trim();
                if (currentKw.length >= 2) {
                    autoSaveTimeout = setTimeout(() => {
                        saveKeyword(input.value.trim());
                    }, 1000);
                }
            });

            // Enter key saves keyword immediately
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    const kw = input.value.trim();
                    if (kw.length >= 2) {
                        saveKeyword(kw);
                    }
                }
            });

            // Focus and click listeners
            input.addEventListener('focus', () => {
                showDropdown();
            });
            input.addEventListener('click', (e) => {
                e.stopPropagation();
                showDropdown();
            });

            // Form submit listener
            if (form) {
                form.addEventListener('submit', () => {
                    const kw = input.value.trim();
                    if (kw) {
                        saveKeyword(kw);
                    }
                });
            }

            // Sync across dropdowns on custom event
            window.addEventListener('giadung:search-history-updated', (e) => {
                const items = e.detail?.items || getLocalStorageHistory();
                renderRecentList(items);
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!container.contains(e.target)) {
                    hideDropdown();
                }
            });

            // Escape key closes dropdown
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    hideDropdown();
                }
            });
        });
    };

    // Global listener: Capture any search form submissions (including mobile)
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form && form.matches && form.matches('form')) {
            const qInput = form.querySelector('input[name="q"]');
            if (qInput && qInput.value) {
                const kw = qInput.value.trim();
                if (kw.length >= 2) {
                    saveKeyword(kw);
                }
            }
        }
    });

    initSearchContainers();
});
