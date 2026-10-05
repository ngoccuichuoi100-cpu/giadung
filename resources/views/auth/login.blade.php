@extends('layouts.auth')

@section('title', 'Tài khoản - GiaDụngPro')

@section('no_logo', true)

@section('wrapper_class', 'w-full max-w-4xl rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl relative overflow-hidden transition-all duration-500')
@section('wrapper_style', 'min-height: 620px; height: 620px; background-color: #B2D5E5;')

@section('content')
<div class="relative w-full overflow-hidden" id="auth-container" style="height: 620px; min-height: 620px; position: relative; width: 100%;">
    
    <!-- Lớp phủ màu Onyx phía trên có thể thay đổi clip-path -->
    <div class="absolute top-0 left-0 w-full h-full bg-onyx z-20 clip-signin-static" id="overlay-bg" style="background-color: #020202;"></div>

    <!-- ========================================== -->
    <!-- 1. FORM ĐĂNG KÝ (Sits on Onyx side when active) -->
    <!-- ========================================== -->
    <div class="absolute top-0 right-0 h-full w-1/2 flex flex-col justify-center pl-16 sm:pl-20 pr-8 sm:pr-10 transition-all duration-500 ease-in-out z-30 opacity-0 transform translate-x-full pointer-events-none" id="signup-container">
        
        <div class="w-full">
            <div class="text-center mb-6">
                <h2 class="text-2xl sm:text-3xl font-extrabold uppercase tracking-tight bg-gradient-to-r from-white via-cyan-100 to-candy-blue bg-clip-text text-transparent cursor-default">
                    Đăng Ký
                </h2>
            </div>

            <form id="form-register" class="space-y-5">
                @csrf
                <!-- Email -->
                <div class="relative">
                    <input type="email" id="email" name="email" placeholder="Email nhận thông báo *" required
                        class="w-full auth-input-line-light">
                    <span class="absolute right-1 bottom-3.5 text-white/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </span>
                    <p class="error-msg text-red-400 text-xs mt-1.5 font-medium hidden" id="err-email"></p>
                </div>

                <!-- Mật khẩu -->
                <div class="relative">
                    <input type="password" id="reg_password" name="password" placeholder="Mật khẩu (Tối thiểu 8 ký tự) *" required
                        class="w-full auth-input-line-light">
                    <span class="absolute right-1 bottom-3.5 text-white/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </span>
                    <p class="error-msg text-red-400 text-xs mt-1.5 font-medium hidden" id="err-password"></p>
                </div>

                <!-- Nhập lại mật khẩu -->
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Xác nhận mật khẩu *" required
                        class="w-full auth-input-line-light">
                    <span class="absolute right-1 bottom-3.5 text-white/60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </span>
                    <p class="error-msg text-red-400 text-xs mt-1.5 font-medium hidden" id="err-password_confirmation"></p>
                </div>

                <!-- Đồng ý điều khoản -->
                <div class="flex items-center justify-between pt-1">
                    <label for="policy" class="flex items-center text-xs text-white/80 select-none cursor-pointer">
                        <input type="checkbox" id="policy" name="policy" required
                            class="w-4 h-4 mr-2 rounded text-candy-blue focus:ring-candy-blue border-white/20 cursor-pointer bg-transparent">
                        <span>Tôi đồng ý với <a href="#" class="font-extrabold text-candy-blue hover:underline">Điều khoản dịch vụ</a></span>
                    </label>
                </div>
                <p class="error-msg text-red-400 text-xs font-medium hidden" id="err-policy"></p>

                <!-- Nút đăng ký -->
                <button type="submit" id="btn-submit-reg"
                    class="w-full py-3 bg-candy-blue hover:bg-candy-blue-hover text-onyx font-bold rounded-full shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2 text-xs uppercase tracking-wider cursor-pointer border-none mt-2">
                    <span>ĐĂNG KÝ</span>
                    <svg class="animate-spin h-5 w-5 text-onyx hidden" id="spinner-reg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-3 flex items-center justify-center">
                <div class="border-t border-white/20 w-full"></div>
                <span class="bg-onyx px-3 text-[11px] font-bold text-white/60 uppercase tracking-widest absolute">Hoặc</span>
            </div>

            <!-- Google Quick Register Button -->
            <a href="{{ route('login.google') }}"
                class="w-full py-2.5 px-4 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-full border border-white/30 backdrop-blur-sm shadow-sm transition-all duration-300 flex items-center justify-center gap-3 text-xs tracking-wide">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Đăng ký nhanh bằng Google</span>
            </a>

            <div class="mt-4 text-center text-xs text-white/70">
                Đã có tài khoản? <span class="font-extrabold text-candy-blue hover:underline cursor-pointer" id="to-signin-link">Đăng nhập ngay</span>
            </div>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- 2. FORM ĐĂNG NHẬP (Sits on Candy Blue side) -->
    <!-- ========================================== -->
    <div class="absolute top-0 left-0 h-full w-1/2 flex flex-col justify-center px-10 sm:px-14 transition-all duration-500 ease-in-out z-30 opacity-100 transform translate-x-0" id="signin-container">
        
        <div class="text-center mb-6">
            <h2 class="text-2xl sm:text-3xl font-extrabold uppercase tracking-tight bg-gradient-to-r from-black via-onyx to-black bg-clip-text text-transparent cursor-default">
                Đăng Nhập
            </h2>
        </div>

        @if($errors->has('login') || $errors->has('password'))
            <div class="bg-red-50/50 border border-red-200 text-red-800 p-3.5 rounded-2xl mb-6 text-xs font-medium">
                @if($errors->has('login'))
                    <p>{{ $errors->first('login') }}</p>
                @endif
                @if($errors->has('password'))
                    <p>{{ $errors->first('password') }}</p>
                @endif
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" id="login-form" class="space-y-4">
            @csrf
            
            <!-- Email/Username -->
            <div class="relative">
                <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus placeholder="Tên đăng nhập hoặc Email"
                    class="w-full auth-input-line-dark">
                <span class="absolute right-1 bottom-3.5 text-onyx/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </span>
            </div>

            <!-- Mật khẩu -->
            <div class="relative">
                <input type="password" id="password" name="password" required placeholder="Mật khẩu"
                    class="w-full auth-input-line-dark">
                <span class="absolute right-1 bottom-3.5 text-onyx/60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </span>
            </div>

            <!-- Ghi nhớ đăng nhập -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center text-onyx/80 text-xs cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded text-onyx focus:ring-onyx border-slate-400 mr-2 cursor-pointer">
                    Ghi nhớ đăng nhập
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3 bg-onyx hover:bg-onyx/90 text-candy-blue font-bold rounded-full shadow-md hover:shadow-lg transition-all duration-300 cursor-pointer border-none uppercase tracking-wider text-xs">
                ĐĂNG NHẬP
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-3 flex items-center justify-center">
            <div class="border-t border-onyx/20 w-full"></div>
            <span class="bg-candy-blue px-3 text-[11px] font-bold text-onyx/60 uppercase tracking-widest absolute">Hoặc</span>
        </div>

        <!-- Google Quick Login Button -->
        <a href="{{ route('login.google') }}"
            class="w-full py-2.5 px-4 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-full shadow-sm hover:shadow-md border border-slate-300 transition-all duration-300 flex items-center justify-center gap-3 text-xs tracking-wide">
            <svg class="w-4 h-4" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Đăng nhập nhanh bằng Google</span>
        </a>

        <div class="mt-4 text-center text-xs text-onyx/70">
            Chưa có tài khoản? <span class="font-extrabold text-onyx hover:underline cursor-pointer" id="to-signup-link">Đăng ký ngay</span>
        </div>

    </div>

    <!-- ========================================== -->
    <!-- 3. KHỐI NỘI DUNG OVERLAY (Sits on top of overlay-bg) -->
    <!-- ========================================== -->
    <!-- Bảng bên Trái (Hiển thị khi đang ở Đăng ký) -->
    <div class="absolute left-0 top-0 w-1/2 h-full flex flex-col items-center justify-center text-center px-4 sm:px-8 transition-all duration-500 ease-in-out opacity-0 -translate-x-full pointer-events-none z-30" id="overlay-left">
        
        <!-- Small Luxury Tag -->
        <span class="px-3.5 py-1.5 bg-onyx/10 text-onyx border border-onyx/20 rounded-full text-[11px] font-bold uppercase tracking-widest inline-block mb-3 shadow-inner">
            ✦ GiaDụngPro Membership ✦
        </span>

        <h1 class="text-xl sm:text-2xl lg:text-3xl font-black uppercase tracking-tight text-onyx leading-tight">
            <span class="block whitespace-nowrap">Chào Mừng Quý Khách</span>
            <span class="block whitespace-nowrap">Đến Với GiaDụngPro</span>
        </h1>
        <div class="w-12 h-1 bg-onyx/80 mx-auto rounded-full shadow-sm my-3"></div>
        <p class="text-xs sm:text-sm text-onyx/85 font-medium leading-relaxed max-w-xs sm:max-w-sm mx-auto mb-6">
            Đăng ký tài khoản thành viên để tận hưởng những đặc quyền ưu đãi đẳng cấp cùng dịch vụ gia dụng cao cấp hàng đầu.
        </p>
        <a href="{{ route('home') }}" class="px-8 py-3 bg-onyx text-candy-blue hover:bg-black hover:text-white font-extrabold rounded-full shadow-lg hover:shadow-xl transition-all duration-300 text-xs uppercase cursor-pointer tracking-wider pointer-events-auto border-none inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Quay Lại Trang Chủ</span>
        </a>
    </div>

    <!-- Bảng bên Phải (Hiển thị khi đang ở Đăng nhập) -->
    <div class="absolute right-0 top-0 w-1/2 h-full flex flex-col items-center justify-center text-center px-8 sm:px-12 transition-all duration-500 ease-in-out opacity-100 translate-x-0 pointer-events-auto z-30" id="overlay-right">
        
        <!-- Thẻ Huy Hiệu Tag -->
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white/10 backdrop-blur-md rounded-full border border-white/20 text-candy-blue text-[11px] font-bold tracking-widest uppercase shadow-inner mb-2">
            <span class="w-2 h-2 rounded-full bg-candy-blue animate-ping"></span>
            ✦ GiaDụngPro Membership ✦
        </div>

        <!-- Modern Gradient Header -->
        <div class="space-y-3">
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight bg-gradient-to-r from-white via-cyan-100 to-candy-blue bg-clip-text text-transparent uppercase leading-tight">
                Chào Mừng Bạn<br/>Quay Trở Lại
            </h1>
            <div class="w-12 h-1 bg-gradient-to-r from-cyan-200 to-candy-blue mx-auto rounded-full shadow-sm opacity-80"></div>
            <p class="text-xs sm:text-sm text-white/75 leading-relaxed max-w-xs sm:max-w-sm mx-auto font-medium">
                Khám phá không gian sống tiện nghi & hiện đại. Nâng tầm ngôi nhà Việt cùng thiết bị gia dụng cao cấp.
            </p>
        </div>

        <!-- Nút Quay Lại Trang Chủ -->
        <a href="{{ route('home') }}" class="px-8 py-3 bg-transparent border border-white/40 hover:bg-candy-blue hover:text-onyx hover:border-candy-blue text-white font-extrabold rounded-full transition-all duration-300 text-xs uppercase cursor-pointer tracking-wider pointer-events-auto inline-flex items-center gap-2 mt-6 shadow-md hover:shadow-xl">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Quay Lại Trang Chủ</span>
        </a>

    </div>

</div>

<!-- CSS animations rúng động khi có lỗi nhập liệu và Toast thông báo -->
<style>
    #auth-wrapper {
        min-height: 620px !important;
        background-color: #B2D5E5 !important;
    }
    #auth-container {
        height: 620px !important;
        min-height: 620px !important;
        position: relative !important;
        width: 100% !important;
        overflow: hidden !important;
    }
    #overlay-bg {
        background-color: #020202 !important;
    }
    .clip-signin-static {
        clip-path: polygon(35% 0%, 100% 0%, 100% 100%, 65% 100%) !important;
    }
    .clip-signup-static {
        clip-path: polygon(65% 0%, 100% 0%, 100% 100%, 35% 100%) !important;
    }
    .morph-sweep {
        clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%) !important;
    }
    .bg-candy-blue {
        background-color: #B2D5E5 !important;
    }
    .bg-onyx {
        background-color: #020202 !important;
    }
    .error-msg {
        animation: errShake 0.3s ease-in-out;
    }
    @keyframes errShake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }
    .toast-notify {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #020202;
        color: #B2D5E5;
        padding: 12px 20px;
        border-radius: 16px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
        border: 1px solid rgba(178,213,229,0.2);
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 100;
    }
    .toast-notify.show {
        transform: translateY(0);
        opacity: 1;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const authContainer = document.getElementById('auth-container');
        const signupContainer = document.getElementById('signup-container');
        const signinContainer = document.getElementById('signin-container');
        const overlayBg = document.getElementById('overlay-bg');
        const overlayLeft = document.getElementById('overlay-left');
        const overlayRight = document.getElementById('overlay-right');

        const signUpBtn = document.getElementById('sign-up-btn');
        const signInBtn = document.getElementById('sign-in-btn');
        const toSignUpLink = document.getElementById('to-signup-link');
        const toSignInLink = document.getElementById('to-signin-link');

        let isTransitioning = false;

        // Hàm trượt và morph đường chéo theo 6 bước của Storyboard
        function setPanelState(isRightActive, immediate = false) {
            if (isTransitioning) return;

            if (immediate) {
                // Áp dụng lớp clip-path tĩnh không chạy animation khi vừa tải trang
                overlayBg.style.transition = 'none';
                
                const elements = [signupContainer, signinContainer, overlayLeft, overlayRight];
                elements.forEach(el => el.style.transition = 'none');

                overlayBg.classList.remove('clip-signin-static', 'clip-signup-static', 'morph-sweep');
                if (isRightActive) {
                    authContainer.classList.add('right-panel-active');
                    overlayBg.classList.add('clip-signup-static');

                    signupContainer.className = "absolute top-0 right-0 h-full w-1/2 flex flex-col justify-center pl-16 sm:pl-20 pr-8 sm:pr-10 z-30 opacity-100 transform translate-x-0 pointer-events-auto transition-all duration-500 ease-in-out";
                    signinContainer.className = "absolute top-0 left-0 h-full w-1/2 flex flex-col justify-center px-10 sm:px-14 z-30 opacity-0 transform -translate-x-full pointer-events-none transition-all duration-500 ease-in-out";
                    
                    overlayLeft.className = "absolute left-0 top-0 w-1/2 h-full flex flex-col items-center justify-center text-center px-12 z-30 opacity-100 transform translate-x-0 pointer-events-auto transition-all duration-500 ease-in-out";
                    overlayRight.className = "absolute right-0 top-0 w-1/2 h-full flex flex-col items-center justify-center text-center px-12 z-30 opacity-0 transform translate-x-full pointer-events-none transition-all duration-500 ease-in-out";
                } else {
                    authContainer.classList.remove('right-panel-active');
                    overlayBg.classList.add('clip-signin-static');

                    signupContainer.className = "absolute top-0 right-0 h-full w-1/2 flex flex-col justify-center pl-16 sm:pl-20 pr-8 sm:pr-10 z-30 opacity-0 transform translate-x-full pointer-events-none transition-all duration-500 ease-in-out";
                    signinContainer.className = "absolute top-0 left-0 h-full w-1/2 flex flex-col justify-center px-10 sm:px-14 z-30 opacity-100 transform translate-x-0 pointer-events-auto transition-all duration-500 ease-in-out";
                    
                    overlayLeft.className = "absolute left-0 top-0 w-1/2 h-full flex flex-col items-center justify-center text-center px-12 z-30 opacity-0 transform -translate-x-full pointer-events-none transition-all duration-500 ease-in-out";
                    overlayRight.className = "absolute right-0 top-0 w-1/2 h-full flex flex-col items-center justify-center text-center px-12 z-30 opacity-100 transform translate-x-0 pointer-events-auto transition-all duration-500 ease-in-out";
                }

                // Force reflow
                authContainer.offsetHeight;

                // Trả lại các transition mặc định sau đó
                setTimeout(() => {
                    overlayBg.style.transition = '';
                    elements.forEach(el => el.style.transition = '');
                }, 50);
                return;
            }

            // --- ANIMATION THEATRICAL SWEEP CURTAIN (6 STEPS) ---
            isTransitioning = true;

            // Bước 1 & 2: Màn Onyx phủ toàn màn hình, các form slide-out biến mất
            overlayBg.classList.remove('clip-signin-static', 'clip-signup-static', 'morph-sweep');
            overlayBg.classList.add('morph-sweep');

            if (isRightActive) {
                // Trượt Đăng nhập ra bên trái
                signinContainer.classList.replace('opacity-100', 'opacity-0');
                signinContainer.classList.replace('translate-x-0', '-translate-x-full');
                signinContainer.classList.replace('pointer-events-auto', 'pointer-events-none');

                // Trượt Hello Friend ra bên phải
                overlayRight.classList.replace('opacity-100', 'opacity-0');
                overlayRight.classList.replace('translate-x-0', 'translate-x-full');
                overlayRight.classList.replace('pointer-events-auto', 'pointer-events-none');
            } else {
                // Trượt Đăng ký ra bên phải
                signupContainer.classList.replace('opacity-100', 'opacity-0');
                signupContainer.classList.replace('translate-x-0', 'translate-x-full');
                signupContainer.classList.replace('pointer-events-auto', 'pointer-events-none');

                // Trượt Welcome Back ra bên trái
                overlayLeft.classList.replace('opacity-100', 'opacity-0');
                overlayLeft.classList.replace('translate-x-0', '-translate-x-full');
                overlayLeft.classList.replace('pointer-events-auto', 'pointer-events-none');
            }

            // Bước 3 & 4: Khi màn Onyx đã phủ kín (sau 300ms), đổi sân khấu cực nhanh phía sau tấm màn
            setTimeout(() => {
                if (isRightActive) {
                    authContainer.classList.add('right-panel-active');

                    // Đặt trước các form mới ở vị trí chờ trượt vào
                    signupContainer.className = "absolute top-0 right-0 h-full w-1/2 flex flex-col justify-center pl-16 sm:pl-20 pr-8 sm:pr-10 z-30 opacity-0 transform translate-x-full pointer-events-none transition-all duration-500 ease-in-out";
                    overlayLeft.className = "absolute left-0 top-0 w-1/2 h-full flex flex-col items-center justify-center text-center px-12 z-30 opacity-0 transform -translate-x-full pointer-events-none transition-all duration-500 ease-in-out";
                } else {
                    authContainer.classList.remove('right-panel-active');

                    signinContainer.className = "absolute top-0 left-0 h-full w-1/2 flex flex-col justify-center px-10 sm:px-14 z-30 opacity-0 transform -translate-x-full pointer-events-none transition-all duration-500 ease-in-out";
                    overlayRight.className = "absolute right-0 top-0 w-1/2 h-full flex flex-col items-center justify-center text-center px-12 z-30 opacity-0 transform translate-x-full pointer-events-none transition-all duration-500 ease-in-out";
                }

                // Force reflow
                authContainer.offsetHeight;

                // Bước 5 & 6: Mở màn quét ngược lại và kéo các form mới trượt vào
                setTimeout(() => {
                    overlayBg.classList.remove('morph-sweep');
                    if (isRightActive) {
                        overlayBg.classList.add('clip-signup-static');

                        // Trượt Đăng ký vào
                        signupContainer.classList.replace('opacity-0', 'opacity-100');
                        signupContainer.classList.replace('translate-x-full', 'translate-x-0');
                        signupContainer.classList.replace('pointer-events-none', 'pointer-events-auto');

                        // Trượt Welcome Back vào
                        overlayLeft.classList.replace('opacity-0', 'opacity-100');
                        overlayLeft.classList.replace('-translate-x-full', 'translate-x-0');
                        overlayLeft.classList.replace('pointer-events-none', 'pointer-events-auto');
                    } else {
                        overlayBg.classList.add('clip-signin-static');

                        // Trượt Đăng nhập vào
                        signinContainer.classList.replace('opacity-0', 'opacity-100');
                        signinContainer.classList.replace('-translate-x-full', 'translate-x-0');
                        signinContainer.classList.replace('pointer-events-none', 'pointer-events-auto');

                        // Trượt Hello Friend vào
                        overlayRight.classList.replace('opacity-0', 'opacity-100');
                        overlayRight.classList.replace('translate-x-full', 'translate-x-0');
                        overlayRight.classList.replace('pointer-events-none', 'pointer-events-auto');
                    }

                    // Giải phóng khóa chuyển đổi sau khi hoàn tất hoạt họa
                    setTimeout(() => {
                        isTransitioning = false;
                    }, 500);
                }, 20);

            }, 300);
        }

        // Bắt đầu dựa trên đường dẫn URL khi load trang
        if (window.location.pathname.includes('register')) {
            setPanelState(true, true);
        } else {
            setPanelState(false, true);
        }

        // Đăng ký các trình nghe sự kiện chuyển đổi
        const handleToSignUp = () => {
            window.history.pushState(null, '', '{{ route("register") }}');
            setPanelState(true);
        };

        const handleToSignIn = () => {
            window.history.pushState(null, '', '{{ route("login") }}');
            setPanelState(false);
        };

        if (signUpBtn) signUpBtn.addEventListener('click', handleToSignUp);
        toSignUpLink.addEventListener('click', handleToSignUp);
        
        if (signInBtn) signInBtn.addEventListener('click', handleToSignIn);
        toSignInLink.addEventListener('click', handleToSignIn);

        // Nút Back/Forward của trình duyệt
        window.addEventListener('popstate', () => {
            if (window.location.pathname.includes('register')) {
                setPanelState(true);
            } else {
                setPanelState(false);
            }
        });

        // ==========================================
        // LOGIC FORM ĐĂNG KÝ AJAX
        // ==========================================
        const formRegister = document.getElementById('form-register');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('reg_password');
        const confirmInput = document.getElementById('password_confirmation');
        const policyInput = document.getElementById('policy');

        const errEmail = document.getElementById('err-email');
        const errPassword = document.getElementById('err-password');
        const errConfirm = document.getElementById('err-password_confirmation');
        const errPolicy = document.getElementById('err-policy');

        const btnSubmitReg = document.getElementById('btn-submit-reg');
        const spinnerReg = document.getElementById('spinner-reg');

        const showToast = (message) => {
            const toast = document.createElement('div');
            toast.className = 'toast-notify';
            toast.innerHTML = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 50);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        };

        const clearErrors = () => {
            document.querySelectorAll('.error-msg').forEach(el => {
                el.classList.add('hidden');
                el.textContent = '';
            });
            document.querySelectorAll('.auth-input-line-light, .auth-input-line-dark').forEach(el => {
                el.classList.remove('border-red-500');
            });
        };

        const displayErrors = (errors, prefix = 'err-') => {
            clearErrors();
            for (const key in errors) {
                let fieldKey = key;
                if (key === 'password' && errors[key][0].includes('nhập lại')) {
                    fieldKey = 'password_confirmation';
                }
                const errorEl = document.getElementById(`${prefix}${fieldKey}`);
                const inputEl = document.getElementById(fieldKey === 'password_confirmation' ? 'password_confirmation' : (fieldKey === 'password' ? 'reg_password' : fieldKey));
                if (errorEl) {
                    errorEl.textContent = errors[key][0];
                    errorEl.classList.remove('hidden');
                }
                if (inputEl) {
                    inputEl.classList.add('border-red-500');
                }
            }
        };

        // Submit form đăng ký
        if (formRegister) {
            formRegister.addEventListener('submit', async (e) => {
                e.preventDefault();
                clearErrors();

                const emailVal = emailInput ? emailInput.value.trim() : '';
                const passwordVal = passwordInput ? passwordInput.value : '';
                const passwordConfirmVal = confirmInput ? confirmInput.value : '';
                const policyChecked = policyInput ? policyInput.checked : false;

                let clientErrors = {};
                if (emailVal === "") {
                    clientErrors.email = ['Vui lòng nhập địa chỉ email.'];
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                    clientErrors.email = ['Địa chỉ email không hợp lệ.'];
                }
                if (passwordVal.length < 8) clientErrors.password = ['Mật khẩu phải có ít nhất 8 ký tự.'];
                if (passwordVal !== passwordConfirmVal) clientErrors.password_confirmation = ['Mật khẩu nhập lại không khớp.'];
                if (!policyChecked) clientErrors.policy = ['Bạn phải đồng ý với chính sách dịch vụ.'];

                if (Object.keys(clientErrors).length > 0) {
                    displayErrors(clientErrors);
                    return;
                }

                btnSubmitReg.disabled = true;
                btnSubmitReg.classList.add('opacity-80');
                spinnerReg.classList.remove('hidden');

                try {
                    const formData = new FormData(formRegister);
                    const response = await fetch('{{ route("register") }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && data.errors) {
                            displayErrors(data.errors);
                        } else {
                            showToast('❌ Đăng ký thất bại: ' + (data.message || 'Lỗi không xác định.'));
                        }
                    } else {
                        showToast('🎉 Đăng ký tài khoản thành công! Đang chuyển hướng...');
                        setTimeout(() => {
                            window.location.href = data.redirect || '{{ route("register.welcome") }}';
                        }, 500);
                    }
                } catch (err) {
                    console.error(err);
                    showToast('❌ Lỗi kết nối mạng.');
                } finally {
                    btnSubmitReg.disabled = false;
                    btnSubmitReg.classList.remove('opacity-80');
                    spinnerReg.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection
