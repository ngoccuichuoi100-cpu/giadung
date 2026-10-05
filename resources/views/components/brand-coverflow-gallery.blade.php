@props(['brands'])

@if($brands && $brands->isNotEmpty())
    <div id="brand-coverflow-container" class="brand-coverflow-container" tabindex="0" role="group" aria-roledescription="carousel" aria-label="Hãng sản phẩm Carousel">
        <div class="brand-coverflow-stage">
            @foreach($brands as $index => $brand)
                <a href="{{ route('products.index', ['brand' => $brand->slug]) }}"
                   class="brand-coverflow-card group bg-[#B2D5E5] dark:bg-[#B2D5E5] border border-white/80 dark:border-white/50 rounded-3xl flex flex-col items-center justify-center p-6 text-center hover:border-white transition-all duration-300 shadow-md hover:shadow-xl"
                   data-index="{{ $index }}"
                   aria-label="{{ $brand->name }}"
                   aria-hidden="true"
                   tabindex="-1">
                    
                    <!-- Logo container -->
                    <div class="w-20 h-20 rounded-2xl bg-white dark:bg-white flex items-center justify-center mb-4 overflow-hidden shadow-sm border border-white/90 dark:border-white/80 group-hover:scale-105 transition-transform duration-300 p-2.5">
                        @if($brand->logo)
                            <img src="{{ $brand->logo_url }}" 
                                 alt="Logo {{ $brand->name }}" 
                                 class="w-full h-full object-contain select-none" 
                                 draggable="false"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-full h-full items-center justify-center bg-white text-slate-800 font-extrabold text-2xl select-none" style="display: none;">
                                {{ substr($brand->name, 0, 1) }}
                            </div>
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-white text-slate-800 font-extrabold text-2xl select-none">
                                {{ substr($brand->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- Brand Name & Product Count -->
                    <h3 class="font-black text-slate-900 text-base group-hover:text-black transition-colors duration-300 select-none">{{ $brand->name }}</h3>
                    <span class="text-xs text-slate-700 mt-1.5 font-bold select-none">{{ $brand->products_count }} sản phẩm</span>

                    <!-- Dim overlay -->
                    <div class="brand-coverflow-overlay"></div>
                </a>
            @endforeach
        </div>
    </div>

    <style>
        .brand-coverflow-container {
            position: relative;
            width: 100%;
            min-height: 380px;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1600px;
            overflow: hidden;
            outline: none;
            padding: 20px 0;
        }

        .brand-coverflow-stage {
            position: relative;
            width: 280px;
            height: 240px;
            transform-style: preserve-3d;
        }

        .brand-coverflow-card {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 280px;
            height: 240px;
            border-radius: 1.5rem;
            overflow: hidden;
            transform-style: preserve-3d;
            transform-origin: center center;
            transform: translate(-50%, -50%);
            opacity: 0;
            z-index: 1;
            box-shadow: 0 12px 30px -8px rgba(178, 213, 229, 0.5), 0 4px 12px rgba(0, 0, 0, 0.06);
            transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1), 
                        opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1), 
                        border-color 0.3s ease, 
                        box-shadow 0.3s ease;
        }

        .brand-coverflow-card[data-index="0"] {
            opacity: 1;
            z-index: 10;
        }

        .dark .brand-coverflow-card {
            box-shadow: 0 12px 35px -8px rgba(178, 213, 229, 0.4), 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .brand-coverflow-overlay {
            position: absolute;
            inset: 0;
            background-color: #1a4258;
            opacity: 0;
            transition: opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1);
            pointer-events: none;
            z-index: 10;
        }

        /* Focus indicators */
        .brand-coverflow-container:focus-visible {
            outline: 2px solid #ff6802;
            outline-offset: 4px;
            border-radius: 1rem;
        }

        .brand-coverflow-card:focus-visible {
            outline: 2px solid #ff6802;
            outline-offset: 2px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('brand-coverflow-container');
            if (!container) return;

            const cards = Array.from(container.querySelectorAll('.brand-coverflow-card'));
            const stage = container.querySelector('.brand-coverflow-stage');
            const n = cards.length;
            if (n === 0) return;

            let activeIndex = 0;
            let autoplayTimer = null;
            let lockRef = false;
            const moveDur = 600; // Animation duration match

            const autoplayEnabled = true;
            const autoplayDelay = 4000; // 4 seconds delay between shifts

            function getParams() {
                const width = window.innerWidth;
                if (width >= 1024) { // Desktop
                    return {
                        cardWidth: 280,
                        cardHeight: 240,
                        gap: 7.5,
                        tilt: 14,
                        sideTilt: 6,
                        depth: 220,
                        scaleStep: 0.15,
                        maxVisible: 2
                    };
                } else if (width >= 768) { // Tablet
                    return {
                        cardWidth: 240,
                        cardHeight: 210,
                        gap: 6.2,
                        tilt: 10,
                        sideTilt: 4,
                        depth: 180,
                        scaleStep: 0.16,
                        maxVisible: 2
                    };
                } else { // Mobile
                    return {
                        cardWidth: 190,
                        cardHeight: 170,
                        gap: 4.2,
                        tilt: 6,
                        sideTilt: 2,
                        depth: 120,
                        scaleStep: 0.18,
                        maxVisible: 1
                    };
                }
            }

            function updateCarousel() {
                const params = getParams();
                const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const dur = prefersReducedMotion ? 0.05 : 0.6;
                const dim = 0.32; // opacity of side cards overlay to preserve vibrant candy blue tone

                // Adjust stage size dynamically for responsiveness
                stage.style.width = `${params.cardWidth}px`;
                stage.style.height = `${params.cardHeight}px`;

                cards.forEach((card, i) => {
                    let rel = i - activeIndex;
                    
                    // Loop wrapping calculations
                    if (rel > n / 2) rel -= n;
                    if (rel < -n / 2) rel += n;

                    const ax = Math.abs(rel);
                    const visible = ax <= params.maxVisible;
                    const isActive = rel === 0;

                    const sc = Math.max(0.4, 1 - ax * params.scaleStep);
                    const tx = rel * (params.gap * 30);
                    const tz = -ax * params.depth;
                    const ry = -rel * params.tilt;
                    const rz = rel * params.sideTilt;

                    // Update styles
                    card.style.width = `${params.cardWidth}px`;
                    card.style.height = `${params.cardHeight}px`;
                    card.style.transition = `transform ${dur}s cubic-bezier(0.22, 1, 0.36, 1), opacity ${dur}s cubic-bezier(0.22, 1, 0.36, 1), border-color 0.3s ease, box-shadow 0.3s ease`;
                    card.style.transform = `translate(-50%, -50%) translateX(${tx}px) translateZ(${tz}px) rotateY(${ry}deg) rotateZ(${rz}deg) scale(${sc})`;
                    card.style.opacity = visible ? 1 : 0;
                    card.style.pointerEvents = visible ? 'auto' : 'none';
                    card.style.cursor = isActive ? 'default' : 'pointer';
                    card.style.zIndex = 10 - ax;

                    // Configure overlay dimming
                    const overlay = card.querySelector('.brand-coverflow-overlay');
                    if (overlay) {
                        overlay.style.transition = `opacity ${dur}s cubic-bezier(0.22, 1, 0.36, 1)`;
                        overlay.style.opacity = isActive ? 0 : dim;
                    }

                    // A11y updates
                    card.setAttribute('aria-hidden', (!visible).toString());
                    if (isActive) {
                        card.setAttribute('tabindex', '0');
                    } else {
                        card.setAttribute('tabindex', '-1');
                    }
                });
            }

            function lockInput() {
                lockRef = true;
                setTimeout(() => {
                    lockRef = false;
                }, Math.max(50, moveDur));
            }

            function setActive(index) {
                if (lockRef) return;
                lockInput();
                activeIndex = (((index) % n) + n) % n;
                updateCarousel();
            }

            function step(dir) {
                setActive(activeIndex + dir);
            }

            // Click interaction with logic separation for active vs inactive card
            cards.forEach((card, i) => {
                card.addEventListener('click', (e) => {
                    if (i !== activeIndex) {
                        e.preventDefault(); // Stop navigation to brand detail
                        setActive(i);       // Bring to focus instead
                    }
                });
            });

            // Keyboard support
            container.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    step(1);
                } else if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    step(-1);
                }
            });

            // Autoplay control loop
            function startAutoplay() {
                if (!autoplayEnabled || n < 2) return;
                stopAutoplay();
                autoplayTimer = setInterval(() => {
                    step(1);
                }, autoplayDelay);
            }

            function stopAutoplay() {
                if (autoplayTimer) {
                    clearInterval(autoplayTimer);
                    autoplayTimer = null;
                }
            }

            // Interactive pause/resume for autoplay
            container.addEventListener('mouseenter', stopAutoplay);
            container.addEventListener('mouseleave', startAutoplay);
            container.addEventListener('focusin', stopAutoplay);
            container.addEventListener('focusout', startAutoplay);

            // Responsive updates on window resize
            let resizeTimeout;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(updateCarousel, 50);
            });

            // Initial render
            updateCarousel();
            startAutoplay();
        });
    </script>
@endif
