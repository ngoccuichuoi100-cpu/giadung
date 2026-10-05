@props(['slug' => '', 'name' => '', 'class' => 'w-7 h-7'])

@php
    $key = strtolower($slug . ' ' . $name);
@endphp

@if(str_contains($key, 'com-dien') || str_contains($key, 'cơm'))
    <!-- Nồi cơm điện -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 11h16a2 2 0 0 1 2 2v5a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3v-5a2 2 0 0 1 2-2z"/>
        <path d="M4 11a8 8 0 0 1 16 0"/>
        <circle cx="12" cy="16" r="1.5"/>
        <path d="M8 3v2M12 2v3M16 3v2"/>
    </svg>
@elseif(str_contains($key, 'noi-chao') || str_contains($key, 'chảo') || str_contains($key, 'nồi') && !str_contains($key, 'chiên') && !str_contains($key, 'lẩu') && !str_contains($key, 'áp suất'))
    <!-- Nồi & chảo -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 11h16a1 1 0 0 1 1 1v4a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5v-4a1 1 0 0 1 1-1z"/>
        <path d="M4 11V8a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v3"/>
        <path d="M9 4h6"/>
    </svg>
@elseif(str_contains($key, 'xay-sinh-to') || str_contains($key, 'sinh tố') || str_contains($key, 'xay-thit') || str_contains($key, 'máy xay'))
    <!-- Máy xay sinh tố / thịt -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 3h6l1.5 11h-9L9 3z"/>
        <path d="M6 14h12v4a3 3 0 0 1-3 3H9a3 3 0 0 1-3-3v-4z"/>
        <path d="M12 17v1"/>
        <path d="M15 7h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-1.5"/>
    </svg>
@elseif(str_contains($key, 'chien') || str_contains($key, 'chiên') || str_contains($key, 'noi-chien') || str_contains($key, 'nồi chiên'))
    <!-- Nồi chiên không dầu -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="4" y="3" width="16" height="18" rx="4"/>
        <path d="M8 8h8"/>
        <rect x="7" y="12" width="10" height="6" rx="2"/>
        <circle cx="12" cy="15" r="1"/>
    </svg>
@elseif(str_contains($key, 'may-ep') || str_contains($key, 'ép') || str_contains($key, 'vat-cam') || str_contains($key, 'vắt cam'))
    <!-- Máy ép / Vắt cam -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 6L6 6l2 14a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2l2-14z"/>
        <line x1="6" y1="6" x2="18" y2="6"/>
        <path d="M15 2l-3 4"/>
        <circle cx="17" cy="5" r="3"/>
    </svg>
@elseif(str_contains($key, 'bep-tu') || str_contains($key, 'bếp từ') || str_contains($key, 'bep-ga') || str_contains($key, 'bếp ga') || str_contains($key, 'hồng ngoại'))
    <!-- Bếp từ / Gas -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="16" rx="3"/>
        <circle cx="8.5" cy="12" r="3"/>
        <circle cx="15.5" cy="12" r="3"/>
        <circle cx="8.5" cy="12" r="1"/>
        <circle cx="15.5" cy="12" r="1"/>
    </svg>
@elseif(str_contains($key, 'vi-song') || str_contains($key, 'vi sóng') || str_contains($key, 'lo-nuong') || str_contains($key, 'lò nướng'))
    <!-- Lò vi sóng / Lò nướng -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="16" rx="3"/>
        <rect x="6" y="8" width="9" height="8" rx="1"/>
        <circle cx="18" cy="9" r="1"/>
        <circle cx="18" cy="13" r="1"/>
        <circle cx="18" cy="17" r="1"/>
    </svg>
@elseif(str_contains($key, 'sua-hat') || str_contains($key, 'sữa hạt'))
    <!-- Máy làm sữa hạt -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M7 4h10v14a3 3 0 0 1-3 3H10a3 3 0 0 1-3-3V4z"/>
        <path d="M7 8h10"/>
        <circle cx="12" cy="14" r="2"/>
        <path d="M10 2h4"/>
    </svg>
@elseif(str_contains($key, 'ca-phe') || str_contains($key, 'cà phê') || str_contains($key, 'coffee'))
    <!-- Máy pha cà phê -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 8h1a4 4 0 1 1 0 8h-1"/>
        <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z"/>
        <line x1="6" y1="2" x2="6" y2="4"/>
        <line x1="10" y1="2" x2="10" y2="4"/>
        <line x1="14" y1="2" x2="14" y2="4"/>
    </svg>
@elseif(str_contains($key, 'am-sieu-toc') || str_contains($key, 'siêu tốc') || str_contains($key, 'binh-dun') || str_contains($key, 'ấm'))
    <!-- Ấm siêu tốc / Bình đun -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 10a6 6 0 0 0 12 0V7H6v3z"/>
        <path d="M6 10l-3 4a1 1 0 0 0 0 1.2l1.6 1.8A1 1 0 0 0 5.4 17H6"/>
        <path d="M18 9h2a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2h-2"/>
        <rect x="4" y="19" width="16" height="2" rx="1"/>
        <path d="M10 4h4"/>
        <path d="M12 4v3"/>
    </svg>
@elseif(str_contains($key, 'noi-lau') || str_contains($key, 'lẩu'))
    <!-- Nồi lẩu điện -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 11h18a1 1 0 0 1 1 1v2a6 6 0 0 1-6 6H8a6 6 0 0 1-6-6v-2a1 1 0 0 1 1-1z"/>
        <path d="M2 13h2M20 13h2"/>
        <path d="M8 7c1 1 1 2 0 3M12 6c1 1 1 2 0 3M16 7c1 1 1 2 0 3"/>
    </svg>
@elseif(str_contains($key, 'ap-suat') || str_contains($key, 'áp suất') || str_contains($key, 'nau-cham') || str_contains($key, 'nấu chậm'))
    <!-- Nồi áp suất / Nấu chậm -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="4" y="8" width="16" height="12" rx="3"/>
        <path d="M2 11h2M20 11h2"/>
        <path d="M7 8V6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2"/>
        <circle cx="12" cy="14" r="2"/>
    </svg>
@elseif(str_contains($key, 'loc-nuoc') || str_contains($key, 'lọc nước') || str_contains($key, 'binh-loc'))
    <!-- Máy lọc nước -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>
        <path d="M12 11v6"/>
        <path d="M9 14h6"/>
    </svg>
@elseif(str_contains($key, 'khong-khi') || str_contains($key, 'không khí'))
    <!-- Máy lọc không khí -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9.59 4.59A2 2 0 1 1 11 8H2m10.59 11.41A2 2 0 1 0 14 16H2m15.73-8.27A2.5 2.5 0 1 1 19.5 12H2"/>
    </svg>
@elseif(str_contains($key, 'nuoc-nong') || str_contains($key, 'nóng lạnh'))
    <!-- Máy nước nóng -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"/>
    </svg>
@elseif(str_contains($key, 'giu-nhiet') || str_contains($key, 'giữ nhiệt') || str_contains($key, 'binh-thuy') || str_contains($key, 'bình thủy'))
    <!-- Bình / Ly giữ nhiệt -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M7 6h10v14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V6z"/>
        <path d="M8 2h8v4H8z"/>
        <line x1="7" y1="10" x2="17" y2="10"/>
    </svg>
@elseif(str_contains($key, 'quat') || str_contains($key, 'quạt') || str_contains($key, 'lam-mat') || str_contains($key, 'làm mát'))
    <!-- Quạt / Làm mát -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <path d="M12 12c1.5-3 4-3.5 5-2s-.5 4-3 4"/>
        <path d="M12 12c-3 1.5-3.5 4-2 5s4-.5 4-3"/>
        <path d="M12 12c-1.5-3-4-3.5-5-2s.5 4 3 4"/>
        <circle cx="12" cy="12" r="2"/>
    </svg>
@elseif(str_contains($key, 'hut-mui') || str_contains($key, 'hút mùi') || str_contains($key, 'hut-khoi'))
    <!-- Máy hút mùi -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 17l4-9h10l4 9H3z"/>
        <path d="M9 8V3h6v5"/>
        <path d="M6 21h12"/>
    </svg>
@elseif(str_contains($key, 'den') || str_contains($key, 'đèn') || str_contains($key, 'chieu-sang') || str_contains($key, 'chiếu sáng'))
    <!-- Đèn / Chiếu sáng -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 18h6m-4 4h2m-4-7a7 7 0 1 1 6 0c-.5 1-1.5 2-1.5 3h-3c0-1-1-2-1.5-3z"/>
    </svg>
@elseif(str_contains($key, 'ban-ui') || str_contains($key, 'bàn ủi'))
    <!-- Bàn ủi -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 17h16l-3-7a2 2 0 0 0-1.8-1H7.8A2 2 0 0 0 6 10L4 17z"/>
        <path d="M8 9V6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v3"/>
        <circle cx="12" cy="14" r="1"/>
    </svg>
@elseif(str_contains($key, 'o-cam') || str_contains($key, 'ổ cắm'))
    <!-- Ổ cắm điện -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="4" y="4" width="16" height="16" rx="4"/>
        <circle cx="9" cy="12" r="1.5"/>
        <circle cx="15" cy="12" r="1.5"/>
    </svg>
@elseif(str_contains($key, 'can-suc-khoe') || str_contains($key, 'cân'))
    <!-- Cân sức khỏe -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="18" height="18" rx="4"/>
        <circle cx="12" cy="9" r="3"/>
        <line x1="12" y1="9" x2="13.5" y2="7.5"/>
    </svg>
@elseif(str_contains($key, 'khoa') || str_contains($key, 'khóa'))
    <!-- Khóa điện tử -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="5" y="11" width="14" height="10" rx="2"/>
        <path d="M8 11V7a4 4 0 0 1 8 0v4"/>
        <circle cx="12" cy="16" r="1.5"/>
    </svg>
@elseif(str_contains($key, 'hut-bui') || str_contains($key, 'hút bụi') || str_contains($key, 'robot'))
    <!-- Robot hút bụi -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="9"/>
        <path d="M12 7v5l3 3"/>
        <circle cx="12" cy="12" r="2"/>
        <path d="M9 18h6"/>
    </svg>
@elseif(str_contains($key, 'rua-chen') || str_contains($key, 'rửa chén') || str_contains($key, 'say-chen'))
    <!-- Máy rửa chén -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="4" y="3" width="16" height="18" rx="3"/>
        <line x1="4" y1="8" x2="20" y2="8"/>
        <circle cx="8" cy="5.5" r="1"/>
        <circle cx="12" cy="5.5" r="1"/>
        <circle cx="12" cy="15" r="3"/>
    </svg>
@elseif(str_contains($key, 'huyet-ap') || str_contains($key, 'huyết áp'))
    <!-- Máy đo huyết áp -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        <path d="M12 10v4M10 12h4"/>
    </svg>
@elseif(str_contains($key, 'say-toc') || str_contains($key, 'sấy tóc'))
    <!-- Máy sấy tóc -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 13h10a4 4 0 0 0 4-4V7a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2a4 4 0 0 0 4 4z"/>
        <path d="M6 13v7a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-7"/>
    </svg>
@elseif(str_contains($key, 'cao-rau') || str_contains($key, 'cạo râu'))
    <!-- Máy cạo râu -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="7" y="10" width="10" height="11" rx="4"/>
        <circle cx="9.5" cy="6" r="2.5"/>
        <circle cx="14.5" cy="6" r="2.5"/>
        <line x1="12" y1="13" x2="12" y2="15"/>
    </svg>
@elseif(str_contains($key, 'massage'))
    <!-- Ghế massage -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M7 19v2m10-2v2M5 11h14v5a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-5z"/>
        <path d="M7 11V6a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v5"/>
        <path d="M3 13v2a2 2 0 0 0 2 2M21 13v2a2 2 0 0 1-2 2"/>
    </svg>
@elseif(str_contains($key, 'xit-rua') || str_contains($key, 'xịt rửa') || str_contains($key, 've-sinh') || str_contains($key, 'vệ sinh'))
    <!-- Máy xịt rửa / Vệ sinh -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2v4m0 0l-3 4h6l-3-4z"/>
        <rect x="7" y="10" width="10" height="11" rx="2"/>
        <path d="M12 14v4M10 16h4"/>
    </svg>
@elseif(str_contains($key, 'danh-trung') || str_contains($key, 'đánh trứng'))
    <!-- Máy đánh trứng -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2v8m-4 5a4 4 0 0 0 8 0c0-3-4-5-4-5s-4 2-4 5z"/>
    </svg>
@else
    <!-- Default Đồ gia dụng -->
    <svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6z"/>
        <circle cx="12" cy="12" r="3"/>
        <path d="M12 2v2M12 20v2M2 12h2M20 12h2"/>
    </svg>
@endif
