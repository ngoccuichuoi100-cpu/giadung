/**
 * Admin Sticky Quick Actions Navbar Engine for GiaDungPro
 * Tự động hiển thị thanh công cụ tiện ích nổi khi Admin cuộn trang xuống,
 * giúp tìm kiếm realtime, thêm mới và cuộn lên đầu trang mà không cần kéo chuột ngược lên.
 */
(function () {
    let mainScrollContainer = null;
    let stickyNav = null;
    let stickySearchInput = null;
    let stickySearchSpinner = null;
    let stickySearchClear = null;
    let stickyCreateBtn = null;
    let stickyCreateBtnText = null;
    let stickyScrollTopBtn = null;
    let stickyTitle = null;
    let stickySubtitle = null;
    let stickyIcon = null;

    let targetMainSearchInput = null;
    let isInitialized = false;

    // Danh sách icon phù hợp theo từng loại trang
    const PAGE_ICONS = {
        'products': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
        'categories': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>',
        'orders': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>',
        'employees': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
        'customers': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',
        'vouchers': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>',
        'sales-schedules': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>'
    };

    function initAdminStickyNav() {
        mainScrollContainer = document.getElementById('adminMainContent');
        stickyNav = document.getElementById('adminStickyQuickNav');

        if (!mainScrollContainer || !stickyNav) return;

        stickySearchInput = document.getElementById('stickyNavSearchInput');
        stickySearchSpinner = document.getElementById('stickyNavSearchSpinner');
        stickySearchClear = document.getElementById('stickyNavSearchClear');
        stickyCreateBtn = document.getElementById('stickyNavCreateBtn');
        stickyCreateBtnText = document.getElementById('stickyNavCreateBtnText');
        stickyScrollTopBtn = document.getElementById('stickyNavScrollTopBtn');
        stickyTitle = document.getElementById('stickyNavTitle');
        stickySubtitle = document.getElementById('stickyNavSubtitle');
        stickyIcon = document.getElementById('stickyNavIcon');
        const searchContainer = document.getElementById('stickyNavSearchContainer');

        // 1. Phân tích trang hiện tại: Kiểm tra xem có bảng dữ liệu / danh sách không
        const currentPath = window.location.pathname.toLowerCase();
        const mainTable = mainScrollContainer.querySelector('table, #adminTableContainer, .overflow-x-auto');
        targetMainSearchInput = mainScrollContainer.querySelector('form input[name="q"]');

        // Nếu là trang tạo mới hoặc chỉnh sửa hoặc dashboard (không có bảng danh sách), ẩn hoàn toàn Sticky Nav
        if (!mainTable && !targetMainSearchInput) {
            hideStickyNav();
            stickyNav.dataset.enabled = 'false';
            return;
        }

        stickyNav.dataset.enabled = 'true';

        // 2. Cập nhật Tiêu đề và Phụ đề
        const pageHeading = mainScrollContainer.querySelector('h2');
        const pageSubHeading = mainScrollContainer.querySelector('h2 + p, h2 ~ p');
        if (pageHeading && stickyTitle) {
            stickyTitle.textContent = pageHeading.textContent.trim();
        }
        if (pageSubHeading && stickySubtitle) {
            stickySubtitle.textContent = pageSubHeading.textContent.trim();
        }

        // 3. Cập nhật Icon động
        if (stickyIcon) {
            let matchedIcon = null;
            for (const [key, svg] of Object.entries(PAGE_ICONS)) {
                if (currentPath.includes(key)) {
                    matchedIcon = svg;
                    break;
                }
            }
            if (matchedIcon) {
                stickyIcon.innerHTML = matchedIcon;
            }
        }

        // 4. Đồng bộ Ô Tìm Kiếm Realtime
        if (targetMainSearchInput && searchContainer && stickySearchInput) {
            searchContainer.classList.remove('hidden');
            stickySearchInput.placeholder = targetMainSearchInput.getAttribute('placeholder') || 'Tìm kiếm nhanh...';
            stickySearchInput.value = targetMainSearchInput.value;
            toggleStickyClearBtn();

            // Lắng nghe gõ trên ô search nổi
            stickySearchInput.oninput = () => {
                targetMainSearchInput.value = stickySearchInput.value;
                toggleStickyClearBtn();
                // Kích hoạt sự kiện input trên form chính để trigger live search
                targetMainSearchInput.dispatchEvent(new Event('input', { bubbles: true }));
            };

            stickySearchInput.onkeydown = (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    targetMainSearchInput.value = stickySearchInput.value;
                    targetMainSearchInput.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));
                }
            };

            // Nút Clear trên thanh nổi
            if (stickySearchClear) {
                stickySearchClear.onclick = () => {
                    stickySearchInput.value = '';
                    targetMainSearchInput.value = '';
                    toggleStickyClearBtn();
                    stickySearchInput.focus();
                    targetMainSearchInput.dispatchEvent(new Event('input', { bubbles: true }));
                };
            }

            // Đồng bộ chiều ngược lại (khi ô search chính thay đổi)
            targetMainSearchInput.addEventListener('input', () => {
                if (stickySearchInput.value !== targetMainSearchInput.value) {
                    stickySearchInput.value = targetMainSearchInput.value;
                    toggleStickyClearBtn();
                }
            });

            // Đồng bộ trạng thái Spinner loading từ form chính
            observeSpinner();
        } else if (searchContainer) {
            searchContainer.classList.add('hidden');
        }

        // 5. Cập nhật Nút Thêm Mới Nhanh
        if (stickyCreateBtn && stickyCreateBtnText) {
            const originalCreateBtn = mainScrollContainer.querySelector('a[href*="/create"], button[onclick*="assignModal"], button[onclick*="Create"]');
            if (originalCreateBtn) {
                stickyCreateBtn.classList.remove('hidden');
                stickyCreateBtn.classList.add('flex');

                if (originalCreateBtn.tagName.toLowerCase() === 'a') {
                    stickyCreateBtn.href = originalCreateBtn.href;
                    stickyCreateBtn.onclick = null;
                } else {
                    stickyCreateBtn.href = 'javascript:void(0)';
                    stickyCreateBtn.onclick = () => originalCreateBtn.click();
                }

                const btnText = originalCreateBtn.textContent.replace('+', '').trim();
                stickyCreateBtnText.textContent = btnText || 'Thêm mới';
            } else {
                stickyCreateBtn.classList.add('hidden');
                stickyCreateBtn.classList.remove('flex');
            }
        }

        // 6. Gắn sự kiện Cuộn Lên Đầu Trang (Scroll to Top)
        if (stickyScrollTopBtn && !stickyScrollTopBtn.dataset.bound) {
            stickyScrollTopBtn.dataset.bound = 'true';
            stickyScrollTopBtn.onclick = () => {
                mainScrollContainer.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            };
        }

        // 7. Gắn lắng nghe sự kiện Scroll Container
        if (!isInitialized) {
            isInitialized = true;
            mainScrollContainer.addEventListener('scroll', handleScroll, { passive: true });
        }

        // Kiểm tra vị trí scroll ban đầu
        handleScroll();
    }

    function toggleStickyClearBtn() {
        if (stickySearchClear && stickySearchInput) {
            if (stickySearchInput.value.trim().length > 0) {
                stickySearchClear.classList.remove('hidden');
            } else {
                stickySearchClear.classList.add('hidden');
            }
        }
    }

    function observeSpinner() {
        const mainSpinner = mainScrollContainer ? mainScrollContainer.querySelector('.admin-search-spinner') : null;
        if (!mainSpinner || !stickySearchSpinner) return;

        const observer = new MutationObserver(() => {
            if (mainSpinner.classList.contains('hidden')) {
                stickySearchSpinner.classList.add('hidden');
            } else {
                stickySearchSpinner.classList.remove('hidden');
            }
        });

        observer.observe(mainSpinner, { attributes: true, attributeFilter: ['class'] });
    }

    function showStickyNav() {
        if (!stickyNav || stickyNav.dataset.enabled === 'false') return;
        stickyNav.classList.remove('-translate-y-20', 'opacity-0', 'pointer-events-none');
        stickyNav.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
    }

    function hideStickyNav() {
        if (!stickyNav) return;
        stickyNav.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
        stickyNav.classList.add('-translate-y-20', 'opacity-0', 'pointer-events-none');
    }

    function handleScroll() {
        if (!mainScrollContainer || !stickyNav || stickyNav.dataset.enabled === 'false') return;

        // Khi người dùng cuộn xuống qua 140px (vượt qua tiêu đề và filter ban đầu)
        if (mainScrollContainer.scrollTop > 140) {
            showStickyNav();
        } else {
            hideStickyNav();
        }
    }

    // Khởi tạo ban đầu
    document.addEventListener('DOMContentLoaded', initAdminStickyNav);

    // Tương thích Admin SPA khi chuyển trang
    document.addEventListener('admin:page-loaded', () => {
        setTimeout(initAdminStickyNav, 60);
    });
})();
