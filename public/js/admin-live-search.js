/**
 * Admin Live Search & Real-time Filter Engine for GiaDungPro
 * Tự động lọc kết quả khớp theo thời gian thực ngay khi gõ từ khóa hoặc thay đổi bộ lọc ở tất cả các trang quản lý
 */
(function () {
    let currentAbortController = null;
    let debounceTimer = null;

    /**
     * Khởi tạo tính năng Live Search & Filter cho toàn bộ form quản lý
     */
    function initAdminLiveFilter() {
        // Tìm tất cả các form lọc trong khu vực admin (có input 'q' hoặc nằm trong header/content admin)
        const filterForms = document.querySelectorAll(
            'form[action*="/admin/categories"], ' +
            'form[action*="/admin/products"], ' +
            'form[action*="/admin/orders"], ' +
            'form[action*="/admin/customers"], ' +
            'form[action*="/admin/employees"], ' +
            'form[action*="/admin/vouchers"], ' +
            'form[action*="/admin/sales-schedules"], ' +
            'form:has(input[name="q"])'
        );

        if (!filterForms.length) return;

        filterForms.forEach(form => {
            if (form.dataset.liveFilterInitialized === 'true') return;
            form.dataset.liveFilterInitialized = 'true';

            const searchInput = form.querySelector('input[name="q"]');
            const selectFilters = form.querySelectorAll('select');
            const dateFilters = form.querySelectorAll('input[type="date"], input[type="month"]');

            // Tìm nút Reset
            const resetButton = Array.from(form.querySelectorAll('a, button')).find(el => {
                const text = el.textContent.trim().toLowerCase();
                return text === 'reset' || text === 'đặt lại' || el.getAttribute('type') === 'reset' || el.title === 'Bỏ lọc';
            });
            if (resetButton) {
                resetButton.setAttribute('data-no-spa', 'true');
            }

            // Xử lý Controls (Spinner & Clear Button) cho Search Input
            let spinner = form.querySelector('.admin-search-spinner, #adminSearchSpinner');
            let clearBtn = form.querySelector('.admin-search-clear, #adminSearchClear');

            if (searchInput) {
                searchInput.setAttribute('autocomplete', 'off');

                // Nếu chưa có container controls bên phải, tự động bổ sung
                if (!spinner || !clearBtn) {
                    const parent = searchInput.parentElement;
                    if (parent) {
                        if (!parent.classList.contains('relative')) {
                            parent.classList.add('relative');
                        }

                        let rightContainer = parent.querySelector('.admin-search-right-controls');
                        if (!rightContainer) {
                            rightContainer = document.createElement('div');
                            rightContainer.className = 'admin-search-right-controls absolute right-2.5 top-1/2 -translate-y-1/2 flex items-center gap-1.5 z-10 pointer-events-auto';
                            rightContainer.innerHTML = `
                                <svg class="admin-search-spinner w-3.5 h-3.5 text-orange-500 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <button type="button" class="admin-search-clear text-slate-400 hover:text-slate-600 focus:outline-none transition-colors ${searchInput.value.trim() ? '' : 'hidden'}" title="Xóa tìm kiếm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            `;
                            parent.appendChild(rightContainer);
                        }

                        spinner = rightContainer.querySelector('.admin-search-spinner');
                        clearBtn = rightContainer.querySelector('.admin-search-clear');
                    }
                }
            }

            const toggleClearBtn = () => {
                if (clearBtn && searchInput) {
                    if (searchInput.value.trim().length > 0) {
                        clearBtn.classList.remove('hidden');
                    } else {
                        clearBtn.classList.add('hidden');
                    }
                }
            };

            const setSpinnerLoading = (isLoading) => {
                if (spinner) {
                    if (isLoading) {
                        spinner.classList.remove('hidden');
                    } else {
                        spinner.classList.add('hidden');
                    }
                }
            };

            // Hàm kích hoạt lọc dữ liệu
            const triggerFilter = (isImmediate = false) => {
                clearTimeout(debounceTimer);
                toggleClearBtn();

                if (!isImmediate) {
                    // Ngay khi người dùng gõ, hiển thị spinner để có phản hồi thị giác tức thì
                    setSpinnerLoading(true);
                }

                const execute = () => {
                    performLiveFetch(form, setSpinnerLoading);
                };

                if (isImmediate) {
                    execute();
                } else {
                    debounceTimer = setTimeout(execute, 260);
                }
            };

            // Sự kiện gõ phím trên ô tìm kiếm
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    triggerFilter(false);
                });

                searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        triggerFilter(true);
                    }
                });
            }

            // Sự kiện chọn dropdown select
            selectFilters.forEach(select => {
                // Gỡ bỏ inline onchange nếu có
                select.removeAttribute('onchange');
                select.addEventListener('change', () => {
                    triggerFilter(true);
                });
            });

            // Sự kiện chọn ngày tháng
            dateFilters.forEach(dateInput => {
                dateInput.addEventListener('change', () => {
                    triggerFilter(true);
                });
            });

            // Nút Clear (X) trong ô tìm kiếm
            if (clearBtn && searchInput) {
                clearBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    searchInput.value = '';
                    searchInput.focus();
                    triggerFilter(true);
                });
            }

            // Chặn submit form mặc định gây reload toàn trang
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                triggerFilter(true);
            });

            // Nút Reset
            if (resetButton) {
                resetButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    form.reset();
                    if (searchInput) searchInput.value = '';
                    selectFilters.forEach(s => s.value = '');
                    dateFilters.forEach(d => d.value = '');
                    toggleClearBtn();
                    triggerFilter(true);
                });
            }
        });

        // Gắn lắng nghe cho link phân trang
        bindPaginationLinks();
    }

    /**
     * Thực hiện gửi request fetch và cập nhật nội dung bảng kết quả
     */
    async function performLiveFetch(form, setSpinnerLoading) {
        if (currentAbortController) {
            currentAbortController.abort();
        }
        currentAbortController = new AbortController();

        const tableContainer = document.getElementById('adminTableContainer') 
            || form.parentElement.querySelector('#adminTableContainer')
            || document.querySelector('.overflow-x-auto')?.parentElement;

        if (setSpinnerLoading) setSpinnerLoading(true);
        if (tableContainer) {
            tableContainer.style.transition = 'opacity 0.15s ease';
            tableContainer.style.opacity = '0.55';
        }

        try {
            const formData = new FormData(form);
            const params = new URLSearchParams();

            for (const [key, value] of formData.entries()) {
                if (value !== '' && value !== null && key !== '_token') {
                    params.append(key, value);
                }
            }

            const baseUrl = form.getAttribute('action') || window.location.pathname;
            const queryString = params.toString();
            const fetchUrl = `${baseUrl}${queryString ? '?' + queryString : ''}`;

            const response = await fetch(fetchUrl, {
                signal: currentAbortController.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html, application/xhtml+xml'
                }
            });

            if (!response.ok) {
                throw new Error('Lỗi tải dữ liệu: ' + response.statusText);
            }

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newTableContainer = doc.getElementById('adminTableContainer')
                || doc.querySelector('.overflow-x-auto')?.parentElement;

            if (tableContainer && newTableContainer) {
                tableContainer.innerHTML = newTableContainer.innerHTML;
            } else {
                // Fallback nếu không tìm thấy container cụ thể
                const currentMain = document.getElementById('adminMainContent');
                const newMain = doc.getElementById('adminMainContent');
                if (currentMain && newMain) {
                    const currentInput = form.querySelector('input[name="q"]');
                    const cursorPosition = currentInput ? currentInput.selectionStart : null;
                    const currentValue = currentInput ? currentInput.value : '';

                    currentMain.innerHTML = newMain.innerHTML;

                    // Phục hồi lại form và focus nếu main bị replace
                    const restoredInput = document.querySelector('input[name="q"]');
                    if (restoredInput) {
                        restoredInput.value = currentValue;
                        restoredInput.focus();
                        if (cursorPosition !== null) {
                            restoredInput.setSelectionRange(cursorPosition, cursorPosition);
                        }
                    }
                }
            }

            // Cập nhật URL trình duyệt (không reload trang)
            window.history.replaceState({ path: fetchUrl }, '', fetchUrl);

            // Gắn lại sự kiện cho phân trang mới
            bindPaginationLinks();

        } catch (err) {
            if (err.name !== 'AbortError') {
                console.error('Lỗi khi live filter:', err);
            }
        } finally {
            if (setSpinnerLoading) setSpinnerLoading(false);
            if (tableContainer) {
                tableContainer.style.opacity = '1';
            }
        }
    }

    /**
     * Chặn click phân trang để load bằng AJAX mượt mà
     */
    function bindPaginationLinks() {
        const paginationLinks = document.querySelectorAll(
            '#adminTableContainer a[href*="page="], ' +
            '#adminTableContainer .pagination a, ' +
            '#adminTableContainer nav[role="navigation"] a'
        );

        paginationLinks.forEach(link => {
            link.setAttribute('data-no-spa', 'true');
            if (link.dataset.paginationBound === 'true') return;
            link.dataset.paginationBound = 'true';

            link.addEventListener('click', async (e) => {
                e.preventDefault();
                const targetUrl = link.getAttribute('href');
                if (!targetUrl) return;

                const tableContainer = document.getElementById('adminTableContainer');
                if (tableContainer) {
                    tableContainer.style.transition = 'opacity 0.15s ease';
                    tableContainer.style.opacity = '0.55';
                }

                try {
                    const res = await fetch(targetUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html, application/xhtml+xml'
                        }
                    });

                    if (res.ok) {
                        const html = await res.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContainer = doc.getElementById('adminTableContainer') 
                            || doc.querySelector('.overflow-x-auto')?.parentElement;

                        if (tableContainer && newContainer) {
                            tableContainer.innerHTML = newContainer.innerHTML;
                            window.history.replaceState({ path: targetUrl }, '', targetUrl);
                            tableContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                            bindPaginationLinks();
                        }
                    }
                } catch (e) {
                    console.error('Lỗi khi phân trang AJAX:', e);
                    window.location.href = targetUrl;
                } finally {
                    if (tableContainer) {
                        tableContainer.style.opacity = '1';
                    }
                }
            });
        });
    }

    // Khởi tạo ban đầu
    document.addEventListener('DOMContentLoaded', initAdminLiveFilter);

    // Hỗ trợ Admin SPA khi đổi trang
    document.addEventListener('admin:page-loaded', initAdminLiveFilter);

    // Hỗ trợ nút Back / Forward trên trình duyệt
    window.addEventListener('popstate', () => {
        const currentUrl = window.location.href;
        fetch(currentUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html, application/xhtml+xml'
            }
        }).then(res => res.text()).then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.getElementById('adminTableContainer');
            const curTable = document.getElementById('adminTableContainer');
            if (curTable && newTable) {
                curTable.innerHTML = newTable.innerHTML;
                bindPaginationLinks();
            }
        }).catch(err => console.error(err));
    });
})();
