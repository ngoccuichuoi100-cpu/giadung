@extends('layouts.admin')

@section('title', 'Chỉnh sửa mã ưu đãi ' . $voucher->code . ' - GiaDụngPro')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb / Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.vouchers.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-orange-500 transition-colors mb-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Quay lại danh sách ưu đãi</span>
            </a>
            <h1 class="text-xl font-black text-slate-800 tracking-tight">Chỉnh sửa mã ưu đãi: <span class="text-orange-600 font-mono">{{ $voucher->code }}</span></h1>
            <p class="text-xs text-slate-400 mt-0.5">Cập nhật thông tin chiết khấu, thời gian và đối tượng thụ hưởng</p>
        </div>
    <!-- Notice Banner -->
    <div class="p-4 rounded-2xl bg-amber-50/90 border border-amber-200/90 text-amber-900 shadow-xs flex items-start gap-3.5">
        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <span class="text-[10px] font-black uppercase tracking-wider text-amber-700">Thông báo nâng cấp</span>
            <p class="text-xs font-bold text-slate-800 mt-0.5">“Tính năng này đang được nâng cấp để mang lại trải nghiệm tốt hơn. Bạn vui lòng quay lại sau nhé!”</p>
        </div>
    </div>

    <!-- Edit Form Card -->
    <form action="{{ route('admin.vouchers.update', $voucher->id) }}" method="POST" class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 1. Mã Code -->
            <div>
                <label for="code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Mã ưu đãi (Voucher Code) <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="code" name="code" value="{{ old('code', $voucher->code) }}" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-mono font-bold uppercase focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                @error('code') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- 2. Tên chương trình -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Tên chương trình ưu đãi <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $voucher->name) }}" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                @error('name') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- 3. Mô tả chi tiết -->
            <div class="md:col-span-2">
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Mô tả ngắn & điều kiện ưu đãi
                </label>
                <textarea id="description" name="description" rows="2"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">{{ old('description', $voucher->description) }}</textarea>
                @error('description') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- 4. Loại ưu đãi (Type) -->
            <div>
                <label for="type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Loại hình ưu đãi <span class="text-rose-500">*</span>
                </label>
                <select id="type" name="type" required onchange="handleTypeChange()"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                    <option value="fixed" {{ old('type', $voucher->type) === 'fixed' ? 'selected' : '' }}>💰 Giảm số tiền cố định (₫)</option>
                    <option value="percent" {{ old('type', $voucher->type) === 'percent' ? 'selected' : '' }}>📊 Giảm theo phần trăm (%)</option>
                    <option value="free_shipping" {{ old('type', $voucher->type) === 'free_shipping' ? 'selected' : '' }}>🚚 Miễn phí vận chuyển (Freeship)</option>
                </select>
                @error('type') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- 5. Cách nhận mã (Acquisition Type) -->
            <div>
                <label for="acquisition_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Cách thức nhận mã <span class="text-rose-500">*</span>
                </label>
                <select id="acquisition_type" name="acquisition_type" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                    <option value="public" {{ old('acquisition_type', $voucher->acquisition_type) === 'public' ? 'selected' : '' }}>🏷️ Công khai (Khách tự nhập tại giỏ hàng)</option>
                    <option value="new_member" {{ old('acquisition_type', $voucher->acquisition_type) === 'new_member' ? 'selected' : '' }}>🎁 Tặng khi đăng ký thành viên mới</option>
                    <option value="game_reward" {{ old('acquisition_type', $voucher->acquisition_type) === 'game_reward' ? 'selected' : '' }}>🎡 Thưởng khi chơi trò chơi / Vòng quay</option>
                    <option value="referral" {{ old('acquisition_type', $voucher->acquisition_type) === 'referral' ? 'selected' : '' }}>🤝 Thưởng khi mời thành viên mới</option>
                    <option value="loyalty_points" {{ old('acquisition_type', $voucher->acquisition_type) === 'loyalty_points' ? 'selected' : '' }}>⭐ Đổi điểm tích lũy</option>
                    <option value="special_gift" {{ old('acquisition_type', $voucher->acquisition_type) === 'special_gift' ? 'selected' : '' }}>💎 Tặng riêng cho khách hàng đặc biệt</option>
                </select>
                @error('acquisition_type') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- 6. Giá trị giảm (Value) -->
            <div>
                <label for="value" id="valueLabel" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Mức giảm giá <span class="text-rose-500">*</span>
                </label>
                <div class="relative flex items-center">
                    <input type="number" id="value" name="value" value="{{ old('value', (float)$voucher->value) }}" min="0" step="any" required
                        class="w-full pl-4 pr-14 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                    <span id="valueUnit" class="absolute right-4 text-xs font-bold text-slate-400">₫</span>
                </div>
                <p id="valueHelper" class="text-[11px] text-slate-400 mt-1.5 font-medium">Số tiền giảm trực tiếp vào tổng tiền hàng.</p>
                @error('value') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- 7. Giảm tối đa (Max Discount Amount) -->
            <div id="maxDiscountContainer">
                <label for="max_discount_amount" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Giảm tối đa (Áp dụng cho % hoặc Freeship)
                </label>
                <div class="relative flex items-center">
                    <input type="number" id="max_discount_amount" name="max_discount_amount" value="{{ old('max_discount_amount', $voucher->max_discount_amount ? (float)$voucher->max_discount_amount : '') }}" min="0" step="1000" placeholder="Để trống nếu không giới hạn"
                        class="w-full pl-4 pr-14 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                    <span class="absolute right-4 text-xs font-bold text-slate-400">₫</span>
                </div>
                @error('max_discount_amount') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- 8. Giá trị đơn tối thiểu (Min Order Value) -->
            <div>
                <label for="min_order_value" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Đơn hàng tối thiểu
                </label>
                <div class="relative flex items-center">
                    <input type="number" id="min_order_value" name="min_order_value" value="{{ old('min_order_value', (float)$voucher->min_order_value) }}" min="0" step="1000"
                        class="w-full pl-4 pr-14 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                    <span class="absolute right-4 text-xs font-bold text-slate-400">₫</span>
                </div>
                @error('min_order_value') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- 9. Lượt sử dụng tối đa (Max uses) -->
            <div>
                <label for="max_uses" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Tổng số lượt sử dụng tối đa
                </label>
                <input type="number" id="max_uses" name="max_uses" value="{{ old('max_uses', $voucher->max_uses) }}" min="0"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                <span class="block text-[11px] text-slate-400 mt-1">Đã dùng: <strong>{{ $voucher->uses }}</strong> lượt</span>
                @error('max_uses') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- 10. Thời gian bắt đầu -->
            <div>
                <label for="start_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Thời gian bắt đầu <span class="text-rose-500">*</span>
                </label>
                <input type="datetime-local" id="start_date" name="start_date" value="{{ old('start_date', $voucher->start_date->format('Y-m-d\TH:i')) }}" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                @error('start_date') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- 11. Thời gian kết thúc -->
            <div>
                <label for="end_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Thời gian kết thúc <span class="text-rose-500">*</span>
                </label>
                <input type="datetime-local" id="end_date" name="end_date" value="{{ old('end_date', $voucher->end_date->format('Y-m-d\TH:i')) }}" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                @error('end_date') <p class="text-xs text-rose-500 mt-1 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- 12. Trạng thái -->
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Trạng thái hoạt động
                </label>
                <div class="flex items-center gap-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="active" {{ old('status', $voucher->status) === 'active' ? 'checked' : '' }} class="text-orange-500 focus:ring-orange-500">
                        <span class="text-sm font-bold text-slate-800">⚡ Đang hoạt động</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="inactive" {{ old('status', $voucher->status) === 'inactive' ? 'checked' : '' }} class="text-orange-500 focus:ring-orange-500">
                        <span class="text-sm font-bold text-slate-500">⏸️ Tạm dừng áp dụng</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Form Submit Actions -->
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
            <a href="{{ route('admin.vouchers.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all">
                Hủy bỏ
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold transition-all shadow-xs shadow-orange-500/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Cập nhật mã ưu đãi</span>
            </button>
        </div>
    </form>
</div>

<script>
    function handleTypeChange() {
        const type = document.getElementById('type').value;
        const valueLabel = document.getElementById('valueLabel');
        const valueUnit = document.getElementById('valueUnit');
        const valueInput = document.getElementById('value');
        const valueHelper = document.getElementById('valueHelper');

        if (type === 'percent') {
            valueLabel.innerHTML = 'Mức giảm phần trăm (%) <span class="text-rose-500">*</span>';
            valueUnit.innerText = '%';
            if (valueHelper) valueHelper.innerText = 'Tỷ lệ % giảm giá trên tổng tiền đơn hàng (VD: 10 nghĩa là giảm 10%).';
            valueInput.placeholder = 'VD: 10';
        } else if (type === 'free_shipping') {
            valueLabel.innerHTML = 'Mức hỗ trợ phí vận chuyển <span class="text-rose-500">*</span>';
            valueUnit.innerText = '₫';
            if (valueHelper) valueHelper.innerText = 'Số tiền phí ship shop sẽ hỗ trợ trừ cho khách (phí ship tiêu chuẩn của shop là 30.000 ₫).';
            valueInput.placeholder = 'VD: 30000';
        } else {
            valueLabel.innerHTML = 'Mức giảm giá tiền mặt <span class="text-rose-500">*</span>';
            valueUnit.innerText = '₫';
            if (valueHelper) valueHelper.innerText = 'Số tiền giảm trực tiếp vào tổng tiền hàng (VD: 50.000 ₫).';
            valueInput.placeholder = 'VD: 50000';
        }
    }

    document.addEventListener('DOMContentLoaded', handleTypeChange);
</script>
@endsection
