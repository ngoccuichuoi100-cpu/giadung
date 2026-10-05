/**
 * Admin Sticky Bar Engine for GiaDungPro
 * Bắt chước chuẩn xác cấu trúc chuyển đổi từ navbar cố định sang navbar chuyển động theo cuộn chuột (như trang chi tiết sản phẩm)
 */
(function () {
    let scrollContainer = null;

    function handleAdminStickyBar() {
        const stickyBar = document.getElementById('admin-sticky-bar');
        const wrapper = document.getElementById('admin-panel-wrapper');
        if (!stickyBar || !wrapper) return;

        // Tính toán vị trí tương tự trang chi tiết sản phẩm (Header Admin cao ~64px)
        const containerTop = scrollContainer ? scrollContainer.getBoundingClientRect().top : 64;
        const rect = wrapper.getBoundingClientRect();
        const isSticking = rect.top <= (containerTop + 2) && rect.bottom > 200;

        const scrollToTopBtn = document.getElementById('sticky-scroll-top-btn');

        if (isSticking) {
            stickyBar.classList.add(
                'shadow-lg',
                'rounded-b-2xl',
                'border-b-2',
                'border-orange-500/30',
                'bg-white/95',
                'backdrop-blur-md',
                'rounded-t-none'
            );
            stickyBar.classList.remove('rounded-t-3xl');
            if (scrollToTopBtn) {
                scrollToTopBtn.classList.remove('opacity-0', 'pointer-events-none', 'scale-90');
                scrollToTopBtn.classList.add('opacity-100', 'pointer-events-auto', 'scale-100');
            }
        } else {
            stickyBar.classList.remove(
                'shadow-lg',
                'rounded-b-2xl',
                'border-b-2',
                'border-orange-500/30',
                'bg-white/95',
                'backdrop-blur-md',
                'rounded-t-none'
            );
            stickyBar.classList.add('rounded-t-3xl');
            if (scrollToTopBtn) {
                scrollToTopBtn.classList.remove('opacity-100', 'pointer-events-auto', 'scale-100');
                scrollToTopBtn.classList.add('opacity-0', 'pointer-events-none', 'scale-90');
            }
        }
    }

    function initListeners() {
        scrollContainer = document.getElementById('adminMainContent');

        if (scrollContainer) {
            scrollContainer.removeEventListener('scroll', handleAdminStickyBar);
            scrollContainer.addEventListener('scroll', handleAdminStickyBar, { passive: true });
        }
        window.removeEventListener('scroll', handleAdminStickyBar);
        window.addEventListener('scroll', handleAdminStickyBar, { passive: true });
        window.removeEventListener('resize', handleAdminStickyBar);
        window.addEventListener('resize', handleAdminStickyBar, { passive: true });

        // Gắn nút cuộn lên đầu trang nếu có
        const scrollToTopBtn = document.getElementById('sticky-scroll-top-btn');
        if (scrollToTopBtn && !scrollToTopBtn.dataset.bound) {
            scrollToTopBtn.dataset.bound = 'true';
            scrollToTopBtn.onclick = () => {
                if (scrollContainer) {
                    scrollContainer.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            };
        }

        // Chạy kiểm tra vị trí ban đầu
        handleAdminStickyBar();
    }

    // Khởi tạo ban đầu
    document.addEventListener('DOMContentLoaded', initListeners);

    // Tương thích Admin SPA khi chuyển trang
    document.addEventListener('admin:page-loaded', () => {
        setTimeout(initListeners, 60);
    });
})();
