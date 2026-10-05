@extends('layouts.client')

@section('title', 'Thông tin cá nhân - GiaDụngPro')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-8">Tài khoản của tôi</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Navigation Sidebar or summary info -->
            <div class="lg:col-span-1 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm h-fit text-center space-y-4">
                <div class="w-20 h-20 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-extrabold text-2xl mx-auto shadow-inner">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="font-bold text-slate-800 text-lg">{{ $user->name }}</h2>
                    <p class="text-slate-400 text-xs mt-0.5">{{ $user->email }}</p>
                    <span class="inline-flex mt-2 px-2.5 py-1 text-[10px] uppercase font-bold tracking-wider bg-orange-100 text-orange-700 rounded-md">Khách Hàng Thân Thiết</span>
                </div>
                <div class="border-t border-slate-100 pt-4 text-left text-xs space-y-2.5 text-slate-500">
                    <div>
                        <strong class="text-slate-700">Ngày đăng ký:</strong>
                        {{ $user->created_at->format('d/m/Y') }}
                    </div>
                    <div>
                        <strong class="text-slate-700">Vai trò:</strong>
                        {{ $user->role === 'admin' ? 'Quản trị viên' : ($user->role === 'sales' ? 'Nhân viên bán hàng' : ($user->role === 'warehouse' ? 'Nhân viên kho' : 'Khách hàng')) }}
                    </div>
                </div>

                <!-- Loyalty Points Balance Card -->
                <div class="bg-gradient-to-br from-orange-500 to-amber-500 rounded-2xl p-4 text-white text-left shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-orange-100 uppercase tracking-wider">Điểm tích lũy</span>
                        <svg class="w-5 h-5 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="text-2xl font-black mt-1">{{ number_format($user->points ?? 0, 0, ',', '.') }} <span class="text-xs font-normal opacity-90">điểm</span></div>
                    <a href="{{ route('games.index') }}" class="inline-block mt-3 text-[11px] font-bold text-white bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-xl transition-all">
                        Đổi thưởng & Săn quà →
                    </a>
                </div>

                <!-- Referral Code Card -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 text-left">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Mã giới thiệu của bạn</div>
                    <div class="flex items-center justify-between gap-2">
                        <span id="referralCodeText" class="font-mono font-black text-slate-800 text-sm tracking-wider bg-white px-2.5 py-1.5 rounded-xl border border-slate-200">
                            {{ $user->referral_code ?: 'GD' . strtoupper(substr(md5($user->id), 0, 6)) }}
                        </span>
                        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('referralCodeText').innerText.trim()); alert('Đã sao chép mã giới thiệu thành công!');" class="text-xs font-bold text-orange-600 hover:text-orange-700 bg-orange-50 hover:bg-orange-100 border border-orange-200 px-3 py-1.5 rounded-xl transition-all">
                            Sao chép
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2 leading-relaxed">
                        Chia sẻ mã giới thiệu cho bạn bè khi đăng ký để cùng nhận điểm thưởng tích lũy.
                    </p>
                </div>
            </div>

            <!-- Update Info & Change Password Forms -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Update profile info form -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                    <h3 class="font-bold text-slate-800 text-lg border-b border-slate-100 pb-3 mb-6">Cập nhật thông tin cá nhân</h3>
                    
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Full name -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Họ và tên</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full px-4 py-2.5 bg-slate-50 border @error('name') border-red-500 @else border-slate-200 @enderror rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1.5">Số điện thoại</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required
                                    class="w-full px-4 py-2.5 bg-slate-50 border @error('phone') border-red-500 @else border-slate-200 @enderror rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Địa chỉ Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-4 py-2.5 bg-slate-50 border @error('email') border-red-500 @else border-slate-200 @enderror rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Default Address Section -->
                        <div class="border-t border-slate-100 dark:border-slate-800 pt-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-bold text-slate-800 dark:text-slate-200">
                                    📍 Địa chỉ nhận hàng mặc định
                                </label>
                                <span class="text-xs text-slate-400">Tự động điền khi thanh toán</span>
                            </div>

                            @if($user->address)
                                <div class="p-3.5 bg-candy-blue/10 dark:bg-slate-800/80 border border-candy-blue/30 dark:border-slate-700 rounded-2xl flex items-start gap-2.5">
                                    <span class="text-emerald-500 font-bold text-sm mt-0.5">✓</span>
                                    <div class="text-xs">
                                        <span class="font-bold text-slate-700 dark:text-slate-300 block mb-0.5">Địa chỉ hiện tại:</span>
                                        <span class="text-slate-700 dark:text-slate-200 font-semibold leading-relaxed">{{ $user->address }}</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Vietnamese Cascading Address Selector -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                <!-- Province -->
                                <div>
                                    <label for="profile_province_code" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Tỉnh / Thành phố</label>
                                    <select id="profile_province_code" name="province_code"
                                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-650 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                                        <option value="">-- Chọn Tỉnh/Thành --</option>
                                        @foreach($provinces as $prov)
                                            <option value="{{ $prov->code }}" {{ old('province_code', $user->province_code) == $prov->code ? 'selected' : '' }}>
                                                {{ $prov->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- District -->
                                <div>
                                    <label for="profile_district_code" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1 flex items-center justify-between">
                                        <span>Quận / Huyện</span>
                                        <span id="profile_district_loading" class="text-xs text-orange-500 font-medium hidden">Tải...</span>
                                    </label>
                                    <select id="profile_district_code" name="district_code"
                                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-650 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all disabled:opacity-60"
                                        {{ old('province_code', $user->province_code) ? '' : 'disabled' }}>
                                        <option value="">-- Chọn Quận/Huyện --</option>
                                    </select>
                                </div>

                                <!-- Ward -->
                                <div>
                                    <label for="profile_ward_code" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1 flex items-center justify-between">
                                        <span>Phường / Xã</span>
                                        <span id="profile_ward_loading" class="text-xs text-orange-500 font-medium hidden">Tải...</span>
                                    </label>
                                    <select id="profile_ward_code" name="ward_code"
                                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-650 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all disabled:opacity-60"
                                        {{ old('district_code', $user->district_code) ? '' : 'disabled' }}>
                                        <option value="">-- Chọn Phường/Xã --</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Specific street address -->
                            <div>
                                <label for="detail_address" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Số nhà, ngõ, tên đường cụ thể</label>
                                <input type="text" id="detail_address" name="detail_address"
                                    value="{{ old('detail_address', $user->detail_address ?? ($user->province_code ? '' : $user->address)) }}"
                                    placeholder="Ví dụ: Số 15, Ngõ 20, Đường Cầu Giấy"
                                    class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-650 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all font-medium text-slate-800 dark:text-slate-100">
                                @error('detail_address')
                                    <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Hidden address fallback to preserve existing manual address if unchanged -->
                            <input type="hidden" id="raw_address" name="address" value="{{ old('address', $user->address) }}">

                            <!-- Preview of combined full address -->
                            <div id="full-address-preview-container" class="hidden p-3.5 bg-candy-blue/15 dark:bg-slate-800 border border-candy-blue/40 dark:border-slate-700 rounded-2xl text-xs">
                                <span class="font-bold text-slate-700 dark:text-slate-300 block mb-0.5">Địa chỉ đầy đủ sẽ được lưu:</span>
                                <p id="full-address-preview-text" class="text-slate-900 dark:text-slate-100 font-bold leading-relaxed"></p>
                            </div>
                        </div>

                        <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-2xl shadow transition-all duration-300">
                            Lưu Thay Đổi
                        </button>
                    </form>
                </div>

                <!-- Change password form -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                    <h3 class="font-bold text-slate-800 text-lg border-b border-slate-100 pb-3 mb-6">Đổi mật khẩu</h3>
                    
                    <form action="{{ route('profile.change-password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1.5">Mật khẩu hiện tại</label>
                            <input type="password" id="current_password" name="current_password" required
                                class="w-full px-4 py-2.5 bg-slate-50 border @error('current_password') border-red-500 @else border-slate-200 @enderror rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- New Password -->
                            <div>
                                <label for="new_password" class="block text-sm font-semibold text-slate-700 mb-1.5">Mật khẩu mới</label>
                                <input type="password" id="new_password" name="new_password" required
                                    class="w-full px-4 py-2.5 bg-slate-50 border @error('new_password') border-red-500 @else border-slate-200 @enderror rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                                @error('new_password')
                                    <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm New Password -->
                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">Xác nhận mật khẩu mới</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                            </div>
                        </div>

                        <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl shadow transition-all duration-300">
                            Đổi Mật Khẩu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const provSelect = document.getElementById('profile_province_code');
            const distSelect = document.getElementById('profile_district_code');
            const wardSelect = document.getElementById('profile_ward_code');
            const detailInput = document.getElementById('detail_address');
            const distLoading = document.getElementById('profile_district_loading');
            const wardLoading = document.getElementById('profile_ward_loading');
            const previewContainer = document.getElementById('full-address-preview-container');
            const previewText = document.getElementById('full-address-preview-text');

            const initialProv = "{{ old('province_code', $user->province_code) }}";
            const initialDist = "{{ old('district_code', $user->district_code) }}";
            const initialWard = "{{ old('ward_code', $user->ward_code) }}";

            function updatePreview() {
                const provText = provSelect && provSelect.selectedIndex > 0 ? provSelect.options[provSelect.selectedIndex].text.trim() : '';
                const distText = distSelect && distSelect.selectedIndex > 0 ? distSelect.options[distSelect.selectedIndex].text.trim() : '';
                const wardText = wardSelect && wardSelect.selectedIndex > 0 ? wardSelect.options[wardSelect.selectedIndex].text.trim() : '';
                const detailText = detailInput ? detailInput.value.trim() : '';

                const parts = [detailText, wardText, distText, provText].filter(Boolean);
                if (parts.length > 0 && (provText || detailText)) {
                    previewText.textContent = parts.join(', ');
                    previewContainer.classList.remove('hidden');
                } else {
                    previewContainer.classList.add('hidden');
                }
            }

            function loadDistricts(provinceCode, selectedDistrict = null) {
                distSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                distSelect.disabled = true;
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                wardSelect.disabled = true;

                if (!provinceCode) {
                    updatePreview();
                    return;
                }

                if (distLoading) distLoading.classList.remove('hidden');
                fetch(`/api/locations/provinces/${provinceCode}/districts`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(d => {
                            const opt = document.createElement('option');
                            opt.value = d.code;
                            opt.textContent = d.name;
                            if (selectedDistrict && d.code == selectedDistrict) {
                                opt.selected = true;
                            }
                            distSelect.appendChild(opt);
                        });
                        distSelect.disabled = false;
                        if (selectedDistrict) {
                            loadWards(selectedDistrict, initialWard);
                        }
                        updatePreview();
                    })
                    .catch(err => console.error(err))
                    .finally(() => {
                        if (distLoading) distLoading.classList.add('hidden');
                    });
            }

            function loadWards(districtCode, selectedWard = null) {
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                wardSelect.disabled = true;

                if (!districtCode) {
                    updatePreview();
                    return;
                }

                if (wardLoading) wardLoading.classList.remove('hidden');
                fetch(`/api/locations/districts/${districtCode}/wards`)
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(w => {
                            const opt = document.createElement('option');
                            opt.value = w.code;
                            opt.textContent = w.name;
                            if (selectedWard && w.code == selectedWard) {
                                opt.selected = true;
                            }
                            wardSelect.appendChild(opt);
                        });
                        wardSelect.disabled = false;
                        updatePreview();
                    })
                    .catch(err => console.error(err))
                    .finally(() => {
                        if (wardLoading) wardLoading.classList.add('hidden');
                    });
            }

            if (provSelect) {
                provSelect.addEventListener('change', function () {
                    loadDistricts(this.value);
                });
            }

            if (distSelect) {
                distSelect.addEventListener('change', function () {
                    loadWards(this.value);
                });
            }

            if (wardSelect) {
                wardSelect.addEventListener('change', updatePreview);
            }

            if (detailInput) {
                detailInput.addEventListener('input', updatePreview);
            }

            // Preload if user already has province_code
            if (initialProv) {
                loadDistricts(initialProv, initialDist);
            } else {
                updatePreview();
            }
        });
    </script>
@endsection
