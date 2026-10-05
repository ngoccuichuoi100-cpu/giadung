@extends('layouts.client')

@section('title', 'Về chúng tôi - GiaDụngPro VIP')

@section('content')
    <!-- Hero Header Banner: Đúng màu chủ đạo Candy Blue (#B2D5E5) của website -->
    <style>
        .about-hero-bg {
            background-color: #B2D5E5 !important;
            background: #B2D5E5 !important;
        }
        .dark .about-hero-bg {
            background-color: #0f172a !important;
            background: #0f172a !important;
        }
    </style>
    <section class="about-hero-bg relative text-slate-900 dark:text-white overflow-hidden py-20 px-4 sm:px-6 lg:px-8 text-center border-b border-[#97C3D7] dark:border-slate-800">
        <!-- Ánh sáng nền tinh tế, hài hòa cùng màu Candy Blue -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-white/40 via-transparent to-transparent pointer-events-none"></div>
        <div class="absolute -top-16 -left-16 w-80 h-80 rounded-full blur-3xl pointer-events-none" style="background-color: rgba(255, 255, 255, 0.45);"></div>
        <div class="absolute -top-16 -right-16 w-80 h-80 rounded-full blur-3xl pointer-events-none" style="background-color: rgba(255, 255, 255, 0.35);"></div>

        <div class="relative max-w-4xl mx-auto space-y-5">
            <!-- Badge thương hiệu cao cấp dạng kính thủy tinh -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/90 dark:bg-slate-900/80 backdrop-blur-md border border-white/70 dark:border-sky-500/30 rounded-full text-xs font-black uppercase tracking-wider shadow-xs text-slate-800 dark:text-slate-200">
                <span class="text-orange-500">✦</span>
                <span class="tracking-wide">Thương Hiệu Gia Dụng Hàng Đầu Việt Nam</span>
                <span class="text-orange-500">✦</span>
            </div>
            
            <!-- Tiêu đề màu đặc sắc nét 100%, không mờ nhạt chữ nào -->
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight leading-tight">
                <span class="text-slate-900 dark:text-white">Về Chúng Tôi — </span>
                <span class="text-[#0284c7] dark:text-sky-400">GiaDụngPro</span>
            </h1>

            <!-- Dải vạch nhấn tinh tế -->
            <div class="w-20 h-1 bg-[#0284c7] dark:bg-sky-400 rounded-full mx-auto shadow-xs"></div>
            
            <!-- Mô tả đậm rõ, dễ đọc -->
            <p class="text-slate-700 dark:text-slate-300 text-base sm:text-lg max-w-2xl mx-auto font-semibold leading-relaxed pt-1">
                Kiến tạo tổ ấm hiện đại, tiện nghi và sang trọng với những giải pháp đồ gia dụng thông minh chính hãng hàng đầu.
            </p>
        </div>
    </section>

    <!-- Main Content Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-20">
        
        <!-- 4 Trụ Cột Cam Kết VIP (Pillars Grid) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-slate-900 border-2 border-slate-200/90 dark:border-candy-blue/40 rounded-3xl p-6 shadow-xl dark:shadow-[0_10px_30px_rgba(178,213,229,0.15)] hover:-translate-y-1.5 transition-all duration-300 backdrop-blur-xl">
                <div class="w-12 h-12 rounded-2xl bg-candy-blue/20 dark:bg-candy-blue/15 text-[#0284C7] dark:text-candy-blue flex items-center justify-center font-bold text-xl mb-4 border border-candy-blue/30 shadow-sm">
                    🛡️
                </div>
                <h3 class="text-base font-black text-slate-900 dark:text-candy-blue uppercase tracking-wider mb-2">100% Chính Hãng</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Cam kết sản phẩm nhập khẩu chính ngạch đầy đủ chứng nhận CO/CQ và hóa đơn VAT.
                </p>
            </div>

            <div class="bg-white dark:bg-slate-900 border-2 border-slate-200/90 dark:border-candy-blue/40 rounded-3xl p-6 shadow-xl dark:shadow-[0_10px_30px_rgba(178,213,229,0.15)] hover:-translate-y-1.5 transition-all duration-300 backdrop-blur-xl">
                <div class="w-12 h-12 rounded-2xl bg-candy-blue/20 dark:bg-candy-blue/15 text-[#0284C7] dark:text-candy-blue flex items-center justify-center font-bold text-xl mb-4 border border-candy-blue/30 shadow-sm">
                    ⚡
                </div>
                <h3 class="text-base font-black text-slate-900 dark:text-candy-blue uppercase tracking-wider mb-2">Bảo Hành Trọn Đời</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Chế độ hậu mãi tận tâm, hỗ trợ kỹ thuật 24/7 và đổi mới trong 30 ngày nếu phát sinh lỗi.
                </p>
            </div>

            <div class="bg-white dark:bg-slate-900 border-2 border-slate-200/90 dark:border-candy-blue/40 rounded-3xl p-6 shadow-xl dark:shadow-[0_10px_30px_rgba(178,213,229,0.15)] hover:-translate-y-1.5 transition-all duration-300 backdrop-blur-xl">
                <div class="w-12 h-12 rounded-2xl bg-candy-blue/20 dark:bg-candy-blue/15 text-[#0284C7] dark:text-candy-blue flex items-center justify-center font-bold text-xl mb-4 border border-candy-blue/30 shadow-sm">
                    💎
                </div>
                <h3 class="text-base font-black text-slate-900 dark:text-candy-blue uppercase tracking-wider mb-2">Đẳng Cấp 5 Sao</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Mỗi sản phẩm đều được chọn lọc khắt khe nhằm mang lại trải nghiệm sống đẳng cấp nhất.
                </p>
            </div>

            <div class="bg-white dark:bg-slate-900 border-2 border-slate-200/90 dark:border-candy-blue/40 rounded-3xl p-6 shadow-xl dark:shadow-[0_10px_35px_rgba(178,213,229,0.15)] hover:-translate-y-1.5 transition-all duration-300 backdrop-blur-xl">
                <div class="w-12 h-12 rounded-2xl bg-candy-blue/20 dark:bg-candy-blue/15 text-[#0284C7] dark:text-candy-blue flex items-center justify-center font-bold text-xl mb-4 border border-candy-blue/30 shadow-sm">
                    🚀
                </div>
                <h3 class="text-base font-black text-slate-900 dark:text-candy-blue uppercase tracking-wider mb-2">Giao Hàng 2H</h3>
                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    Hệ thống vận chuyển hỏa tốc nội thành, đóng gói chống sốc an toàn tuyệt đối.
                </p>
            </div>
        </div>

        <!-- Section 1: Giới Thiệu & Tầm Nhìn Chiến Lược -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-stretch">
            <div class="lg:col-span-7 bg-white dark:bg-slate-900 border-2 border-slate-200/90 dark:border-candy-blue/40 rounded-3xl p-8 sm:p-10 shadow-xl flex flex-col justify-between space-y-6">
                <div class="space-y-4">
                    <span class="px-3.5 py-1 bg-orange-500/10 text-orange-500 rounded-full text-xs font-extrabold uppercase tracking-wider inline-block">
                        HÀNH TRÌNH PHÁT TRIỂN
                    </span>
                    <h2 class="text-3xl font-black text-slate-900 dark:text-candy-blue uppercase tracking-tight">
                        Giới Thiệu Cửa Hàng
                    </h2>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed font-medium">
                        {{ $aboutData['intro'] ?? 'GiaDụngPro tự hào là nhà bán lẻ các thiết bị nhà bếp, nồi cơm, máy xay chính hãng cao cấp hàng đầu Việt Nam.' }}
                    </p>
                    <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed font-medium">
                        {{ $aboutData['vision'] ?? 'Trở thành điểm đến tin cậy nhất cho các thiết bị thông minh tiện ích nâng tầm cuộc sống.' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Năm Thành Lập</span>
                        <span class="text-2xl font-black text-slate-900 dark:text-candy-blue">2018</span>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Khách Hàng Phục Vụ</span>
                        <span class="text-2xl font-black text-slate-900 dark:text-candy-blue">100.000+</span>
                    </div>
                </div>
            </div>

            <!-- Tầm Nhìn Quote Box VIP (Tự động thích ứng màu chữ & nền hoàn hảo ở cả Chế độ Sáng và Tối) -->
            <div class="lg:col-span-5 bg-white dark:bg-slate-900 border-2 border-slate-200/90 dark:border-candy-blue/40 rounded-3xl p-8 sm:p-10 shadow-xl flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-48 h-48 bg-candy-blue/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="space-y-4 relative z-10">
                    <span class="px-3.5 py-1 bg-candy-blue/15 text-[#0284C7] dark:text-candy-blue border border-candy-blue/30 rounded-full text-xs font-extrabold uppercase tracking-widest inline-block shadow-sm">
                        ✦ TRIẾT LÝ VẬN HÀNH ✦
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-candy-blue leading-tight">
                        Vạn Sự Tiện Nghi — Nâng Tầm Cuộc Sống
                    </h3>
                    <p class="text-slate-600 dark:text-slate-300 text-sm italic leading-relaxed pt-2">
                        "Chúng tôi cam kết chỉ phân phối sản phẩm chính hãng 100%, bảo hành lâu dài và chăm sóc khách hàng trọn đời. Mỗi sản phẩm được bán ra là một lời hứa về chất lượng và sự tận tâm."
                    </p>
                </div>
                <div class="pt-8 relative z-10 flex items-center gap-3 border-t border-slate-100 dark:border-slate-800 mt-6">
                    <div class="w-10 h-10 rounded-full bg-candy-blue/20 dark:bg-candy-blue/15 text-[#0284C7] dark:text-candy-blue flex items-center justify-center font-bold text-sm border border-candy-blue/30">
                        GD
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-900 dark:text-white uppercase block">Ban Giám Đốc GiaDụngPro</span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Cam kết chất lượng trọn đời</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Lịch Sử Thành Lập & Timeline Cột Mốc -->
        <div class="bg-white dark:bg-slate-900 border-2 border-slate-200/90 dark:border-candy-blue/40 rounded-3xl p-8 sm:p-12 shadow-xl space-y-8">
            <div class="max-w-2xl space-y-3">
                <span class="px-3.5 py-1 bg-cyan-500/10 text-cyan-500 dark:text-cyan-300 rounded-full text-xs font-extrabold uppercase tracking-wider inline-block">
                    CỘT MỐC ĐÁNG NHỚ
                </span>
                <h2 class="text-3xl font-black text-slate-900 dark:text-candy-blue uppercase tracking-tight">
                    Lịch Sử Thành Lập & Định Hướng Phát Triển
                </h2>
                <p class="text-slate-600 dark:text-slate-350 text-sm font-medium leading-relaxed">
                    {{ $aboutData['history'] ?? 'Được thành lập từ năm 2018, chúng tôi đã phục vụ hơn 100,000 gia đình Việt với hơn 500 mẫu mã thiết bị gia dụng hiện đại.' }}
                </p>
            </div>

            <!-- Timeline Grid 4 Cột mốc -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 pt-4">
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                    <span class="text-xs font-black text-orange-500 block">2018 — KHỞI ĐẦU</span>
                    <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Khai Trương Showroom Đầu Tiên</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Thành lập cửa hàng chính hãng đầu tiên tại Cầu Giấy, Hà Nội.</p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                    <span class="text-xs font-black text-cyan-500 block">2021 — MỞ RỘNG</span>
                    <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Cột Mốc 50.000 Khách Hàng</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Mở rộng chi nhánh TP.HCM và triển khai hệ thống tư vấn mua sắm online.</p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                    <span class="text-xs font-black text-emerald-500 block">2024 — VINH DANH</span>
                    <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Top 10 Thương Hiệu Uy Tín</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Nhận giải thưởng Cúp Thương Hiệu Việt Nam Tin Dùng 2024.</p>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 space-y-2">
                    <span class="text-xs font-black text-candy-blue block">2026 — BỨC PHÁ</span>
                    <h4 class="font-extrabold text-sm text-slate-900 dark:text-white">Hệ Sinh Thái Đồ Gia Dụng Số 1</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Tích hợp vòng quay may mắn, hệ thống tích điểm và bảo hành điện tử 4.0.</p>
                </div>
            </div>
        </div>

        <!-- Section 3: Địa Chỉ Showroom & Thành Tựu Giải Thưởng -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Địa chỉ cửa hàng -->
            <div class="bg-white dark:bg-slate-900 border-2 border-slate-200/90 dark:border-candy-blue/40 rounded-3xl p-8 shadow-xl space-y-6">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-2xl bg-orange-500/10 text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-xl text-slate-900 dark:text-candy-blue uppercase tracking-tight">Hệ Thống Showroom</h3>
                        <p class="text-xs text-slate-500">Đón tiếp quý khách tất cả các ngày trong tuần (8:00 - 21:30)</p>
                    </div>
                </div>

                <ul class="space-y-4">
                    @foreach($aboutData['addresses'] as $address)
                        <li class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                            <span class="text-xl shrink-0 mt-0.5">📍</span>
                            <div>
                                <span class="font-bold text-sm text-slate-900 dark:text-white block">{{ $address }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 mt-1 block">Hotline tư vấn: 1900 6868 (Miễn phí cuộc gọi)</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Thành tựu giải thưởng -->
            <div class="bg-white dark:bg-slate-900 border-2 border-slate-200/90 dark:border-candy-blue/40 rounded-3xl p-8 shadow-xl space-y-6">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-2xl bg-amber-500/10 text-amber-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-black text-xl text-slate-900 dark:text-candy-blue uppercase tracking-tight">Thành Tựu & Giải Thưởng</h3>
                        <p class="text-xs text-slate-500">Chứng nhận chất lượng uy tín qua các năm</p>
                    </div>
                </div>

                <ul class="space-y-4">
                    @foreach($aboutData['achievements'] as $ach)
                        <li class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60">
                            <span class="text-xl shrink-0 mt-0.5">🏆</span>
                            <div>
                                <span class="font-bold text-sm text-slate-900 dark:text-white block">{{ $ach }}</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 mt-1 block">Được bình chọn bởi Hiệp hội Tiêu dùng & Ban ngành Việt Nam</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Section 4: Đối Tác Thương Hiệu Đồng Hành -->
        <div class="bg-white dark:bg-slate-900 border-2 border-slate-200/90 dark:border-candy-blue/40 rounded-3xl p-8 sm:p-12 shadow-xl space-y-8">
            <div class="text-center max-w-xl mx-auto space-y-2">
                <span class="px-3.5 py-1.5 bg-candy-blue/15 text-[#0284C7] dark:text-candy-blue border border-candy-blue/30 rounded-full text-xs font-extrabold uppercase tracking-widest inline-block">
                    ✦ ĐỐI TÁC CHIẾN LƯỢC ✦
                </span>
                <h2 class="text-3xl font-black text-slate-900 dark:text-candy-blue uppercase tracking-tight">
                    Thương Hiệu Đồng Hành
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm font-medium">Hợp tác trực tiếp với các tập đoàn gia dụng hàng đầu thế giới</p>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6">
                @forelse($partners as $partner)
                    <div class="group bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/60 p-6 rounded-2xl text-center hover:border-candy-blue transition-all duration-300 flex flex-col justify-center items-center">
                        <div class="w-14 h-14 rounded-2xl bg-white dark:bg-white border border-slate-100 dark:border-slate-200 flex items-center justify-center overflow-hidden mb-3 shadow-sm group-hover:scale-110 transition-transform duration-300 p-2">
                            @if($partner->logo)
                                <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" class="w-full h-full object-contain">
                            @else
                                <span class="text-[#0284C7] dark:text-candy-blue font-black text-lg">{{ substr($partner->name, 0, 1) }}</span>
                            @endif
                        </div>
                        <span class="font-extrabold text-slate-900 dark:text-white text-sm block">{{ $partner->name }}</span>
                        <span class="text-[10px] text-slate-400 font-semibold mt-1 block uppercase tracking-wider">
                            {{ $partner->type === 'current' ? 'Hợp tác chiến lược' : 'Đối tác phân phối' }}
                        </span>
                    </div>
                @empty
                    <!-- Standard Baseline Top Brands fallback -->
                    @php
                        $defaultBrands = [
                            ['name' => 'Philips', 'type' => 'Hà Lan'],
                            ['name' => 'Tefal', 'type' => 'Pháp'],
                            ['name' => 'Panasonic', 'type' => 'Nhật Bản'],
                            ['name' => 'Sunhouse', 'type' => 'Việt Nam'],
                            ['name' => 'Toshiba', 'type' => 'Nhật Bản'],
                        ];
                    @endphp
                    @foreach($defaultBrands as $b)
                        <div class="group bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/60 p-6 rounded-2xl text-center hover:border-candy-blue transition-all duration-300 flex flex-col justify-center items-center">
                            <div class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-700 flex items-center justify-center overflow-hidden mb-3 shadow-sm group-hover:scale-110 transition-transform duration-300">
                                <span class="text-[#0284C7] dark:text-candy-blue font-black text-xl">{{ substr($b['name'], 0, 1) }}</span>
                            </div>
                            <span class="font-extrabold text-slate-900 dark:text-white text-sm block">{{ $b['name'] }}</span>
                            <span class="text-[10px] text-slate-400 font-semibold mt-1 block uppercase tracking-wider">Thương hiệu {{ $b['type'] }}</span>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>

    </div>
@endsection

