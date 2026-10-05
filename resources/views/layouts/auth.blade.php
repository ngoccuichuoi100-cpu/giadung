<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tài khoản - GiaDụngPro')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-icon.png') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Force light mode
        document.documentElement.classList.remove('dark');
        document.cookie = 'theme=light; path=/; max-age=31536000';
    </script>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 bg-slate-100 text-slate-800" style="min-height: 100vh;">

    <div id="auth-wrapper" class="@yield('wrapper_class', 'w-full max-w-md bg-white rounded-3xl border border-slate-200 shadow-xl p-8 transition-colors duration-300')" style="@yield('wrapper_style', '')">
        @hasSection('no_logo')
        @else
            <!-- Logo Branding -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                    <img src="{{ asset('images/logo-transparent.png') }}" alt="GiaDụng" class="h-12 w-auto object-contain group-hover:scale-105 transition-transform duration-300">
                    <span class="font-extrabold text-2xl tracking-tight text-slate-900">GiaDụng</span>
                </a>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 p-3 rounded-2xl mb-4 text-sm shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        @endif

        @yield('content')
    </div>

</body>
</html>
