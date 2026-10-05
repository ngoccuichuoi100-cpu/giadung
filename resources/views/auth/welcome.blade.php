<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chào mừng thành viên mới - GiaDụngPro</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <!-- Google Fonts: Plus Jakarta Sans for UI, Playfair Display for Elegant Editorial Text -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500;1,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .serif-title {
            font-family: 'Playfair Display', serif;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col items-center justify-center p-6 text-white transition-colors duration-300 overflow-hidden relative" style="background: linear-gradient(135deg, #050508 0%, #2b1102 45%, #853100 80%, #ff6802 100%);">
    
    <!-- Decorative glowing orb behind the content for depth -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[40rem] h-[40rem] bg-[#ff6802]/20 rounded-full blur-[140px] pointer-events-none"></div>

    <div id="welcome-container" class="text-center max-w-7xl w-full space-y-16 relative z-10">
        <!-- Greeting Block (Fades in first) - Maximum editorial font size -->
        <div id="greeting-block" class="opacity-0 translate-y-4 transition-all duration-1000 ease-out">
            <p class="serif-title text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-medium px-4 text-slate-100 md:whitespace-nowrap">
                Chào mừng <span class="font-bold italic" style="color: #ff6802;">{{ $user->name }}</span> trở thành khách hàng của Gia Dụng
            </p>
            @if(!empty($user->email) && !str_ends_with($user->email, '@temp.giadung.com'))
            <p class="text-sm sm:text-base text-slate-300/90 mt-4 max-w-xl mx-auto font-normal">
                ✉️ Email xác nhận <strong>"Bạn đã tạo tài khoản thành công"</strong> đã được gửi đến <span class="font-semibold text-[#ff6802]">{{ $user->email }}</span>.
            </p>
            @endif
        </div>

        <!-- Continue Button (Fades in slowly after the greeting) - Text only with no background -->
        <div id="button-block" class="opacity-0 translate-y-4 transition-all duration-1000 ease-out pt-4">
            <a href="{{ route('home') }}" id="btn-continue"
                class="group inline-flex items-center justify-center gap-2 text-white hover:text-[#ff6802] font-extrabold text-lg transition-all duration-300 hover:scale-105 active:scale-95 tracking-wide">
                <span>Tiếp tục</span>
                <svg class="w-5 h-5 transition-transform duration-300 transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        </div>
    </div>

    <style>
        /* Responsive design and animation accessibility features */
        @media (prefers-reduced-motion: reduce) {
            #greeting-block, #button-block {
                transition: none !important;
                transform: none !important;
                opacity: 1 !important;
            }
            #btn-continue {
                transition: none !important;
                transform: none !important;
            }
            #btn-continue svg {
                transition: none !important;
                transform: none !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const greetingBlock = document.getElementById('greeting-block');
            const buttonBlock = document.getElementById('button-block');
            const btnContinue = document.getElementById('btn-continue');

            // 1. Fade-in greeting block first
            setTimeout(() => {
                greetingBlock.classList.remove('opacity-0', 'translate-y-4');
                greetingBlock.classList.add('opacity-100', 'translate-y-0');
            }, 300);

            // 2. Then fade-in button block slowly (delay 1.2s after greeting starts)
            setTimeout(() => {
                buttonBlock.classList.remove('opacity-0', 'translate-y-4');
                buttonBlock.classList.add('opacity-100', 'translate-y-0');
            }, 1500);

            // 3. Handle transition on continue click (Fade-out animation before redirect)
            if (btnContinue) {
                btnContinue.addEventListener('click', (e) => {
                    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                        return; // proceed with normal redirection
                    }

                    e.preventDefault();
                    
                    // Fade out whole container smoothly
                    const container = document.getElementById('welcome-container');
                    container.style.transition = 'all 0.5s ease-in';
                    container.style.opacity = '0';
                    container.style.transform = 'translateY(8px)';
                    
                    setTimeout(() => {
                        window.location.href = btnContinue.getAttribute('href');
                    }, 500);
                });
            }
        });
    </script>
</body>
</html>
