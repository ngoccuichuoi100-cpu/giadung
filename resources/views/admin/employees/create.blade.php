@extends('layouts.admin')

@section('title', 'Thêm nhân viên mới - GiaDụngPro')

@section('content')
    <div class="mt-6 max-w-3xl mx-auto bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs space-y-6 text-left">
        <!-- Header -->
        <div class="flex justify-between items-center border-b border-slate-100 pb-4">
            <div>
                <h2 class="font-bold text-slate-800 text-lg">Thêm tài khoản nhân viên</h2>
                <p class="text-xs text-slate-400 mt-0.5">Tạo tài khoản phân quyền cho bộ phận Kho hàng hoặc Bán hàng</p>
            </div>
            <a href="{{ route('admin.employees.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all">
                ← Quay lại
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.employees.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">Họ và tên nhân viên <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Ví dụ: Nguyễn Văn A"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label for="username" class="block text-xs font-bold text-slate-700 mb-1.5">Tên đăng nhập (Username) <span class="text-red-500">*</span></label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required placeholder="Ví dụ: sales1, kho_hn"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500 @error('username') border-red-500 @enderror">
                    @error('username')
                        <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone (Bắt buộc) -->
                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 mb-1.5">Số điện thoại <span class="text-red-500">*</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="Ví dụ: 0912345678"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email (Không bắt buộc) -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Địa chỉ Email <span class="text-slate-400 font-normal">(Không bắt buộc)</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Ví dụ: nhanvien@giadung.com (nếu có)"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-xs font-bold text-slate-700 mb-1.5">Vai trò làm việc <span class="text-red-500">*</span></label>
                    <select id="role" name="role" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                        <option value="sales" {{ old('role') === 'sales' ? 'selected' : '' }}>Nhân viên Bán hàng (Sales / CSKH)</option>
                        <option value="warehouse" {{ old('role') === 'warehouse' ? 'selected' : '' }}>Nhân viên Kho (Warehouse)</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">Mật khẩu ban đầu <span class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" required placeholder="Nhập ít nhất 3 ký tự..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Residence Address (Nơi cư trú) -->
                <div>
                    <label for="residence_address" class="block text-xs font-bold text-slate-700 mb-1.5">Nơi cư trú của nhân sự</label>
                    <input type="text" id="residence_address" name="residence_address" value="{{ old('residence_address') }}" placeholder="Ví dụ: Số 123 đường ABC, Phường X, Quận Y"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                </div>

                <!-- Workplace / Branch -->
                <div>
                    <label for="workplace" class="block text-xs font-bold text-slate-700 mb-1.5">Nơi làm việc / Chi nhánh</label>
                    <input type="text" id="workplace" name="workplace" value="{{ old('workplace', old('address')) }}" placeholder="Ví dụ: Tổng kho GiaDụngPro hoặc Showroom 1"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-orange-500">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.employees.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                    Lưu tài khoản
                </button>
            </div>
        </form>
    </div>
@endsection