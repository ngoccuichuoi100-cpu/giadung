/**
 * Admin SPA Page Transition Engine for GiaDungPro
 * Provides smooth, instant page transitions without full page reloads.
 */
document.addEventListener('DOMContentLoaded', () => {
    const mainEl = document.getElementById('adminMainContent');
    const progressBar = document.getElementById('adminTopProgressBar');
    const headerLeftEl = document.getElementById('adminHeaderLeftArea');

    if (!mainEl || !progressBar) return;

    let progressTimer = null;
    let isNavigating = false;

    // Progress Bar Controllers
    const startProgress = () => {
        clearInterval(progressTimer);
        progressBar.style.transition = 'width 0.3s ease, opacity 0.2s ease';
        progressBar.style.opacity = '1';
        progressBar.style.width = '25%';

        let currentWidth = 25;
        progressTimer = setInterval(() => {
            if (currentWidth < 80) {
                currentWidth += Math.random() * 12;
                progressBar.style.width = `${currentWidth}%`;
            }
        }, 150);
    };

    const finishProgress = () => {
        clearInterval(progressTimer);
        progressBar.style.transition = 'width 0.2s ease, opacity 0.25s ease';
        progressBar.style.width = '100%';

        setTimeout(() => {
            progressBar.style.opacity = '0';
            setTimeout(() => {
                progressBar.style.width = '0%';
            }, 250);
        }, 200);
    };

    // Update Sidebar Active Class
    const updateSidebarActiveState = (targetUrl) => {
        try {
            const urlObj = new URL(targetUrl, window.location.origin);
            const targetPath = urlObj.pathname.replace(/\/$/, '');

            document.querySelectorAll('.admin-nav-link').forEach(link => {
                const linkHref = link.getAttribute('href');
                if (!linkHref) return;

                const linkPath = new URL(linkHref, window.location.origin).pathname.replace(/\/$/, '');
                
                let isMatch = false;
                if (linkPath === '/admin' || linkPath === '/admin/dashboard') {
                    isMatch = targetPath === '/admin' || targetPath === '/admin/dashboard';
                } else if (linkPath.startsWith('/admin/')) {
                    isMatch = targetPath === linkPath || targetPath.startsWith(linkPath + '/');
                }

                if (isMatch) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        } catch (e) {
            console.error('Sidebar active update error:', e);
        }
    };

    // Core SPA Navigator
    const navigateTo = async (url, pushHistory = true) => {
        if (isNavigating) return;
        isNavigating = true;

        startProgress();

        // 1. Smooth Fade Out Content
        mainEl.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
        mainEl.style.opacity = '0';
        mainEl.style.transform = 'translateY(6px)';

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html, application/xhtml+xml'
                }
            });

            // If redirect or non-OK response, fallback to standard navigation
            if (!response.ok || response.redirected) {
                window.location.href = response.url || url;
                return;
            }

            const html = await response.text();
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');

            const newMain = newDoc.getElementById('adminMainContent') || newDoc.querySelector('main');
            if (!newMain) {
                window.location.href = url;
                return;
            }

            // 2. Update Page Title
            document.title = newDoc.title;

            // 3. Update Header Left Area (Breadcrumb & Title)
            const newHeaderLeft = newDoc.getElementById('adminHeaderLeftArea');
            if (headerLeftEl && newHeaderLeft) {
                headerLeftEl.innerHTML = newHeaderLeft.innerHTML;
            }

            // 4. Update Pending Orders Count Notification Badge
            const currentOrderBadge = document.querySelector('header a[href*="admin/orders"]');
            const newOrderBadge = newDoc.querySelector('header a[href*="admin/orders"]');
            if (currentOrderBadge && newOrderBadge) {
                currentOrderBadge.innerHTML = newOrderBadge.innerHTML;
                currentOrderBadge.setAttribute('title', newOrderBadge.getAttribute('title') || '');
            }

            // 5. Replace Main Content
            mainEl.innerHTML = newMain.innerHTML;
            mainEl.scrollTop = 0;

            // 6. Update Sidebar Active State
            updateSidebarActiveState(url);

            // 7. Push or Replace History
            if (pushHistory) {
                window.history.pushState({ path: url }, '', url);
            }

            // 8. Re-evaluate and run any scripts in new content
            const scriptsToRun = newDoc.querySelectorAll('main script, body script:not([src*="admin-spa"]):not([src*="chart.js"]):not([src*="app.js"])');
            scriptsToRun.forEach(oldScript => {
                try {
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    if (oldScript.src) {
                        newScript.src = oldScript.src;
                    } else {
                        newScript.textContent = oldScript.textContent;
                    }
                    document.body.appendChild(newScript);
                    setTimeout(() => newScript.remove(), 100);
                } catch (e) {
                    console.error('Error running page script:', e);
                }
            });

            // Dispatch global event for page-specific components to re-init
            document.dispatchEvent(new CustomEvent('admin:page-loaded', { detail: { url } }));

            // 9. Smooth Fade In New Content
            setTimeout(() => {
                mainEl.style.opacity = '1';
                mainEl.style.transform = 'translateY(0)';
                finishProgress();
                isNavigating = false;
            }, 50);

        } catch (error) {
            console.error('SPA navigation error, falling back to full reload:', error);
            finishProgress();
            isNavigating = false;
            window.location.href = url;
        }
    };

    // Intercept Link Clicks
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link) return;

        // Skip modified clicks (Ctrl, Cmd, Shift, Alt)
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;

        // Skip target="_blank", download links, or data-no-spa
        if (link.target === '_blank' || link.hasAttribute('download') || link.dataset.noSpa !== undefined) return;

        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

        try {
            const urlObj = new URL(href, window.location.origin);

            // Only intercept internal admin links
            if (urlObj.origin === window.location.origin && urlObj.pathname.startsWith('/admin')) {
                // If same URL with hash, allow default jump
                if (urlObj.pathname === window.location.pathname && urlObj.search === window.location.search && urlObj.hash) {
                    return;
                }

                e.preventDefault();
                navigateTo(urlObj.href, true);
            }
        } catch (err) {
            // Ignore malformed URLs
        }
    });

    // Handle Browser Back / Forward buttons (Popstate)
    window.addEventListener('popstate', () => {
        navigateTo(window.location.href, false);
    });
});
