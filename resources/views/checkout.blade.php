@extends('layouts.client')

@section('title', 'Thanh toán đơn hàng - GiaDụngPro')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-8">Thanh toán đơn hàng</h1>

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-sm font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('checkout.place-order') }}" method="POST" id="checkout-form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Receiver Information & Payments -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Delivery Info Card -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-xs space-y-6">
                        <h2 class="font-bold text-slate-800 text-lg border-b border-slate-100 pb-3">Thông tin nhận hàng</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Receiver Name -->
                            <div>
                                <label for="receiver_name" class="block text-sm font-semibold text-slate-700 mb-1.5">Họ tên người nhận</label>
                                <input type="text" id="receiver_name" name="receiver_name" value="{{ old('receiver_name', $user->name) }}" required
                                    class="w-full px-4 py-2.5 bg-slate-50 border @error('receiver_name') border-red-500 @else border-slate-200 @enderror rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                                @error('receiver_name')
                                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Receiver Phone -->
                            <div>
                                <label for="receiver_phone" class="block text-sm font-semibold text-slate-700 mb-1.5">Số điện thoại</label>
                                <input type="text" id="receiver_phone" name="receiver_phone" value="{{ old('receiver_phone', $user->phone) }}" required
                                    class="w-full px-4 py-2.5 bg-slate-50 border @error('receiver_phone') border-red-500 @else border-slate-200 @enderror rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                                @error('receiver_phone')
                                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Receiver Email -->
                        <div>
                            <label for="receiver_email" class="block text-sm font-semibold text-slate-700 mb-1.5">Địa chỉ Email</label>
                            <input type="email" id="receiver_email" name="receiver_email" value="{{ old('receiver_email', $user->email) }}" required
                                class="w-full px-4 py-2.5 bg-slate-50 border @error('receiver_email') border-red-500 @else border-slate-200 @enderror rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                            @error('receiver_email')
                                <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Vietnamese Cascading Address Selector -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Province -->
                            <div>
                                <label for="province_code" class="block text-sm font-semibold text-slate-700 mb-1.5">Tỉnh / Thành phố</label>
                                <select id="province_code" name="province_code" required
                                    class="w-full px-4 py-2.5 bg-slate-50 border @error('province_code') border-red-500 @else border-slate-200 @enderror rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                                    <option value="">-- Chọn Tỉnh/Thành --</option>
                                    @foreach($provinces as $prov)
                                        <option value="{{ $prov->code }}" {{ old('province_code', $user->province_code) == $prov->code ? 'selected' : '' }}>
                                            {{ $prov->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('province_code')
                                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- District -->
                            <div>
                                <label for="district_code" class="block text-sm font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                                    <span>Quận / Huyện</span>
                                    <span id="district-loading" class="text-xs text-orange-500 font-medium hidden">Đang tải...</span>
                                </label>
                                <select id="district_code" name="district_code" required disabled
                                    class="w-full px-4 py-2.5 bg-slate-50 border @error('district_code') border-red-500 @else border-slate-200 @enderror rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all disabled:opacity-60">
                                    <option value="">-- Chọn Quận/Huyện --</option>
                                </select>
                                @error('district_code')
                                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Ward -->
                            <div>
                                <label for="ward_code" class="block text-sm font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                                    <span>Phường / Xã</span>
                                    <span id="ward-loading" class="text-xs text-orange-500 font-medium hidden">Đang tải...</span>
                                </label>
                                <select id="ward_code" name="ward_code" required disabled
                                    class="w-full px-4 py-2.5 bg-slate-50 border @error('ward_code') border-red-500 @else border-slate-200 @enderror rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all disabled:opacity-60">
                                    <option value="">-- Chọn Phường/Xã --</option>
                                </select>
                                @error('ward_code')
                                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Specific Address -->
                        <div>
                            <label for="address" class="block text-sm font-semibold text-slate-700 mb-1.5">Số nhà, ngõ, tên đường cụ thể</label>
                            <input type="text" id="address" name="address" value="{{ old('address', $user->detail_address ?? ($user->province_code ? '' : $user->address)) }}" placeholder="Ví dụ: Số 15, Ngõ 20, Đường Cầu Giấy" required
                                class="w-full px-4 py-2.5 bg-slate-50 border @error('address') border-red-500 @else border-slate-200 @enderror rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                            @error('address')
                                <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Estimated Delivery Badge -->
                        <div id="est-delivery-container" class="p-4 bg-orange-50/60 border border-orange-100 rounded-2xl hidden">
                            <span class="text-xs font-bold text-orange-700 uppercase tracking-wider block mb-1">Thời gian nhận hàng dự kiến</span>
                            <p id="est-delivery-text" class="text-xs text-orange-850 text-orange-900 font-medium"></p>
                        </div>

                        <!-- Note -->
                        <div>
                            <label for="note" class="block text-sm font-semibold text-slate-700 mb-1.5">Ghi chú giao hàng (không bắt buộc)</label>
                            <textarea id="note" name="note" rows="2" placeholder="Ghi chú về giờ giấc giao hàng..."
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">{{ old('note') }}</textarea>
                        </div>

                        <!-- Receipt Option Section -->
                        <div class="border-t border-slate-100 pt-6 space-y-4">
                            <h3 class="font-bold text-slate-800 text-sm">Hình thức nhận biên lai / Hóa đơn</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="flex items-start p-4 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100/50 transition-all select-none">
                                    <input type="radio" name="receipt_type" value="paper" checked class="w-4 h-4 text-orange-500 focus:ring-orange-500 border-slate-300 mt-0.5 mr-3">
                                    <div>
                                        <span class="block text-sm font-bold text-slate-800">Biên lai giấy</span>
                                        <span class="block text-xs text-slate-400 mt-0.5">Biên lai in giấy sẽ được gửi đính kèm cùng với gói hàng của bạn.</span>
                                    </div>
                                </label>
                                
                                <label class="flex items-start p-4 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100/50 transition-all select-none">
                                    <input type="radio" name="receipt_type" value="electronic" class="w-4 h-4 text-orange-500 focus:ring-orange-500 border-slate-300 mt-0.5 mr-3">
                                    <div>
                                        <span class="block text-sm font-bold text-slate-800">Biên lai điện tử (E-Receipt)</span>
                                        <span class="block text-xs text-slate-400 mt-0.5">Hóa đơn điện tử PDF sẽ được tự động gửi qua hòm thư điện tử.</span>
                                    </div>
                                </label>
                            </div>

                            <!-- E-Receipt Email Address input (Hidden initially) -->
                            <div id="receipt-email-container" class="hidden">
                                <label for="receipt_email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email nhận hóa đơn điện tử</label>
                                <input type="email" id="receipt_email" name="receipt_email" value="{{ old('receipt_email', $user->email) }}"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all">
                                @error('receipt_email')
                                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Payment Choice Card -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-xs space-y-6">
                        <h2 class="font-bold text-slate-800 text-lg border-b border-slate-100 pb-3">Phương thức thanh toán</h2>

                        <div class="space-y-4">
                            <!-- PayOS Automatic Payment Option -->
                            <label class="flex items-start p-4 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100/50 transition-all select-none payment-label" id="label-payos">
                                <input type="radio" name="payment_method" value="payos" class="w-4 h-4 text-orange-500 focus:ring-orange-500 border-slate-300 mt-0.5 mr-3">
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="block text-sm font-bold text-slate-800">Thanh toán tự động qua PayOS (VietQR Pro)</span>
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-extrabold rounded-full uppercase tracking-wider">Tự động 24/7</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">Khớp đơn tức thì</span>
                                        </div>
                                    </div>
                                    <span class="block text-xs text-slate-500 mt-1">Quét mã VietQR thanh toán tự động qua mọi ứng dụng ngân hàng & ví điện tử (Techcombank, Vietcombank, MBBank, BIDV,...).</span>
                                    
                                    <div class="mt-4 p-4 bg-white rounded-2xl border border-blue-100 space-y-3 hidden payment-detail-panel" id="panel-payos">
                                        <div class="flex items-start gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-blue-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            </div>
                                            <div class="text-xs text-slate-600">
                                                <p class="font-bold text-slate-900 text-sm">Xác nhận thanh toán tự động 100%</p>
                                                <p class="text-[11px] text-slate-500 mt-0.5">Sau khi xác nhận đặt hàng, bạn sẽ được chuyển đến trang thanh toán bảo mật PayOS để quét mã QR chuyển khoản chính xác tới từng đồng mà không cần nhập số tài khoản thủ công.</p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[11px] pt-1">
                                            <div class="flex items-center gap-1.5 p-2 bg-slate-50 rounded-xl text-slate-700">
                                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Không cần chụp ủy nhiệm chi</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 p-2 bg-slate-50 rounded-xl text-slate-700">
                                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Kích hoạt đơn hàng ngay</span>
                                            </div>
                                            <div class="flex items-center gap-1.5 p-2 bg-slate-50 rounded-xl text-slate-700">
                                                <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                <span>Bảo mật chuẩn NAPAS / PayOS</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- COD Option -->
                            <label class="flex items-start p-4 bg-slate-50 border border-orange-500 rounded-2xl cursor-pointer hover:bg-slate-100/50 transition-all select-none payment-label" id="label-cod">
                                <input type="radio" name="payment_method" value="cod" checked class="w-4 h-4 text-orange-500 focus:ring-orange-500 border-slate-300 mt-0.5 mr-3">
                                <div class="flex-grow">
                                    <span class="block text-sm font-bold text-slate-800">Thanh toán khi nhận hàng (COD)</span>
                                    <span class="block text-xs text-slate-400 mt-0.5">Giao hàng và thu tiền mặt trực tiếp tại nhà của quý khách.</span>
                                    
                                    <div class="mt-3 text-xs text-slate-500 bg-white p-3 rounded-xl border border-slate-100 font-medium payment-detail-panel" id="panel-cod">
                                        Vui lòng chuẩn bị sẵn số tiền mặt tương ứng để thanh toán cho shipper khi nhận được gói hàng.
                                    </div>
                                </div>
                            </label>

                            <!-- Bank Transfer Option -->
                            <label class="flex items-start p-4 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100/50 transition-all select-none payment-label" id="label-bank_transfer">
                                <input type="radio" name="payment_method" value="bank_transfer" class="w-4 h-4 text-orange-500 focus:ring-orange-500 border-slate-300 mt-0.5 mr-3">
                                <div class="flex-grow">
                                    <span class="block text-sm font-bold text-slate-800">Chuyển khoản ngân hàng (VietQR)</span>
                                    <span class="block text-xs text-slate-400 mt-0.5">Quét mã QR qua ứng dụng ngân hàng di động để thanh toán nhanh chóng.</span>
                                    
                                    <div class="mt-4 p-4 bg-white rounded-2xl border border-slate-100 flex flex-col sm:flex-row items-center gap-6 hidden payment-detail-panel" id="panel-bank_transfer">
                                        <!-- QR code container -->
                                        <div class="w-36 h-36 bg-slate-50 border border-slate-200 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center p-1 relative">
                                            <img id="vietqr-img" src="" alt="Mã VietQR Chuyển khoản" class="w-full h-full object-contain">
                                        </div>
                                        <div class="space-y-2 text-xs text-slate-655 text-slate-700 flex-grow">
                                            <p class="font-bold text-slate-800 text-sm">Thông tin tài khoản nhận tiền:</p>
                                            <p>Ngân hàng: <strong class="text-slate-900">Techcombank (TCB)</strong></p>
                                            <p>Số tài khoản: <strong class="text-slate-900">19028374829012</strong></p>
                                            <p>Chủ tài khoản: <strong class="text-slate-900">CONG TY TNHH GIADUNGPRO</strong></p>
                                            <p>Số tiền: <strong class="text-orange-500 font-extrabold" id="qr-amount-text"></strong></p>
                                            <p>Nội dung CK: <strong class="text-slate-950 px-2 py-0.5 bg-slate-100 rounded border border-slate-200 font-bold" id="qr-syntax-text">GD_PHONE</strong></p>
                                            <p class="text-[10px] text-slate-400 italic">Quý khách quét mã QR để điền tự động số tiền và nội dung chuyển khoản nhanh nhất.</p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Installment Option -->
                            <label class="flex items-start p-4 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-100/50 transition-all select-none payment-label" id="label-installment">
                                <input type="radio" name="payment_method" value="installment" class="w-4 h-4 text-orange-500 focus:ring-orange-500 border-slate-300 mt-0.5 mr-3">
                                <div class="flex-grow">
                                    <span class="block text-sm font-bold text-slate-800">Trả góp kỳ hạn linh hoạt</span>
                                    <span class="block text-xs text-slate-400 mt-0.5">Kỳ hạn trả góp hỗ trợ 3 tháng, 6 tháng và 12 tháng với lãi suất 0%.</span>
                                    
                                    <div class="mt-4 p-4 bg-white rounded-2xl border border-slate-100 space-y-4 hidden payment-detail-panel" id="panel-installment">
                                        <div>
                                            <label for="installment_months" class="block text-xs font-semibold text-slate-600 mb-1.5">Chọn kỳ hạn trả góp:</label>
                                            <select id="installment_months" name="installment_months"
                                                class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white">
                                                <option value="3" selected>3 Tháng (Lãi suất 0%)</option>
                                                <option value="6">6 Tháng (Lãi suất 0%)</option>
                                                <option value="12">12 Tháng (Lãi suất 0%)</option>
                                            </select>
                                        </div>
                                        <div class="p-3 bg-orange-50/50 border border-orange-100 rounded-xl">
                                            <p class="text-xs text-slate-700">
                                                Số tiền dự kiến thanh toán mỗi tháng: 
                                                <strong class="text-sm text-orange-500 font-extrabold block mt-0.5" id="monthly-installment-text">0 ₫ / tháng</strong>
                                            </p>
                                            <span class="text-[10px] text-slate-400 italic mt-1 block">* Chưa bao gồm phí chuyển đổi giao dịch từ ngân hàng phát hành (nếu có).</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Order Details Confirmation -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm h-fit space-y-6">
                    <h3 class="font-bold text-slate-800 text-lg border-b border-slate-100 pb-3">Đơn hàng của bạn</h3>

                    <!-- Mini Cart Items List -->
                    <div class="space-y-4 max-h-60 overflow-y-auto divide-y divide-slate-100 pr-1">
                        @foreach($cartItems as $index => $item)
                            <div class="flex items-center gap-3 pt-3 {{ $index === 0 ? 'pt-0' : '' }}">
                                <div class="w-12 h-12 bg-slate-50 border border-slate-200 rounded-xl overflow-hidden flex-shrink-0">
                                    <img src="{{ $item->variant && $item->variant->thumbnail ? $item->variant->thumbnail_url : $item->product->thumbnail_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-grow min-w-0">
                                    <h4 class="font-bold text-slate-800 text-xs truncate">{{ $item->product->name }}</h4>
                                    <span class="text-slate-400 text-[10px] font-semibold uppercase block">{{ $item->product->brand ?? 'GiaDụngPro' }}</span>
                                    @if($item->variant)
                                        <span class="text-[10px] text-slate-500 block">
                                            @if($item->color) {{ $item->color->name }} @endif
                                            @if($item->size) - {{ $item->size->name }} @endif
                                        </span>
                                    @endif
                                    @if($item->warranty)
                                        <span class="text-[10px] text-emerald-600 block">
                                            BH: {{ $item->warranty->name }}
                                        </span>
                                    @endif
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs text-slate-500 font-medium">SL: {{ $item->quantity }}</span>
                                        <span class="text-xs text-orange-500 font-bold">{{ number_format($item->line_price, 0, ',', '.') }} ₫</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Coupon Input Panel -->
                    <div class="border-t border-slate-100 pt-4 space-y-2">
                        <label for="coupon_code_input" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Mã giảm giá</label>
                        <div class="flex gap-2">
                            <input type="text" id="coupon_code_input" placeholder="Ví dụ: PROMO20" 
                                class="flex-grow px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-orange-500 uppercase">
                            <button type="button" id="btn-apply-coupon" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-all shadow-xs">
                                Áp dụng
                            </button>
                            <button type="button" id="btn-cancel-coupon" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-655 text-red-650 font-bold text-xs rounded-xl transition-all border border-red-200 hidden">
                                Hủy mã
                            </button>
                        </div>
                        <p id="coupon-message" class="text-[10px] font-medium hidden mt-1.5"></p>
                        <!-- Hidden input to post the verified coupon code -->
                        <input type="hidden" name="coupon_code" id="coupon_code_hidden" value="">

                        @if(isset($vouchers) && $vouchers->isNotEmpty())
                            <div class="mt-3 pt-3 border-t border-slate-100">
                                <span class="text-xs font-bold text-slate-700 block mb-2">Voucher của bạn:</span>
                                <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                                    @foreach($vouchers as $uv)
                                        @if($uv->voucher)
                                            <div class="flex items-center justify-between p-2.5 bg-orange-50/60 border border-orange-200/80 rounded-xl text-xs">
                                                <div class="min-w-0 pr-2">
                                                    <span class="font-bold text-orange-600 block truncate">{{ $uv->voucher->code }}</span>
                                                    <span class="text-[11px] text-slate-600 block truncate">{{ $uv->voucher->name }} (Giảm {{ $uv->voucher->type === 'percent' ? $uv->voucher->value . '%' : number_format($uv->voucher->value, 0, ',', '.') . '₫' }})</span>
                                                    <span class="text-[10px] text-slate-400">Đơn từ {{ number_format($uv->voucher->min_order_value, 0, ',', '.') }}₫</span>
                                                </div>
                                                <button type="button" onclick="selectVoucher('{{ $uv->voucher->code }}')" class="px-2.5 py-1 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-lg transition-colors text-[11px] shrink-0">
                                                    Dùng
                                                </button>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Pricing Summary -->
                    <div class="border-t border-slate-100 pt-4 space-y-3 text-sm text-slate-600">
                        <div class="flex justify-between">
                            <span>Tạm tính</span>
                            <span class="font-bold text-slate-800" id="subtotal-val" data-value="{{ $subtotal }}">{{ number_format($subtotal, 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between text-rose-600 hidden" id="discount-row">
                            <span>Giảm giá sản phẩm</span>
                            <span class="font-bold" id="discount-val">-0 ₫</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Phí vận chuyển</span>
                            <span class="font-bold text-slate-800" id="shipping-val" data-value="{{ $shippingFee }}">
                                @if($shippingFee === 0)
                                    Miễn phí
                                @else
                                    {{ number_format($shippingFee, 0, ',', '.') }} ₫
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between text-emerald-600 hidden" id="shipping-discount-row">
                            <span>Giảm phí vận chuyển</span>
                            <span class="font-bold" id="shipping-discount-val">-0 ₫</span>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-4 flex justify-between text-base font-extrabold text-slate-900">
                        <span>Tổng thanh toán</span>
                        <span class="text-xl text-orange-500" id="total-amount-val" data-value="{{ $totalAmount }}">{{ number_format($totalAmount, 0, ',', '.') }} ₫</span>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-extrabold text-sm rounded-2xl shadow-md hover:shadow-lg transition-all duration-300">
                        Xác Nhận Đặt Hàng
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- JavaScript block for AJAX Cascading Select, Coupons, Payment changes, and QR Codes -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Elements
            const provinceSelect = document.getElementById('province_code');
            const districtSelect = document.getElementById('district_code');
            const wardSelect = document.getElementById('ward_code');
            const receiverPhone = document.getElementById('receiver_phone');
            
            const districtLoading = document.getElementById('district-loading');
            const wardLoading = document.getElementById('ward-loading');
            
            const estDeliveryContainer = document.getElementById('est-delivery-container');
            const estDeliveryText = document.getElementById('est-delivery-text');

            const receiptTypeRadios = document.getElementsByName('receipt_type');
            const receiptEmailContainer = document.getElementById('receipt-email-container');
            const receiptEmailInput = document.getElementById('receipt_email');

            const paymentRadios = document.getElementsByName('payment_method');
            const paymentLabels = document.querySelectorAll('.payment-label');
            const paymentDetailPanels = document.querySelectorAll('.payment-detail-panel');

            // Coupon elements
            const couponInput = document.getElementById('coupon_code_input');
            const applyCouponBtn = document.getElementById('btn-apply-coupon');
            const cancelCouponBtn = document.getElementById('btn-cancel-coupon');
            const couponMessage = document.getElementById('coupon-message');
            const couponHiddenInput = document.getElementById('coupon_code_hidden');
            const discountRow = document.getElementById('discount-row');
            const discountVal = document.getElementById('discount-val');
            const shippingDiscountRow = document.getElementById('shipping-discount-row');
            const shippingDiscountVal = document.getElementById('shipping-discount-val');
            const subtotalValEl = document.getElementById('subtotal-val');
            const shippingValEl = document.getElementById('shipping-val');
            const totalValEl = document.getElementById('total-amount-val');

            // VietQR specific elements
            const vietqrImg = document.getElementById('vietqr-img');
            const qrAmountText = document.getElementById('qr-amount-text');
            const qrSyntaxText = document.getElementById('qr-syntax-text');

            // Installment specific elements
            const installmentSelect = document.getElementById('installment_months');
            const monthlyInstallmentText = document.getElementById('monthly-installment-text');

            // CSRF Token header
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // 1. Dynamic Address Cascading Select & Est Delivery Load
            provinceSelect.addEventListener('change', function () {
                const provinceCode = this.value;
                
                // Clear and disable downstream selectors
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                districtSelect.disabled = true;
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                wardSelect.disabled = true;
                
                // Hide est delivery
                estDeliveryContainer.classList.add('hidden');

                if (!provinceCode) return;

                // Load districts
                districtLoading.classList.remove('hidden');
                fetch(`/api/locations/provinces/${provinceCode}/districts`)
                    .then(response => {
                        if (!response.ok) throw new Error('Không thể tải dữ liệu quận huyện.');
                        return response.json();
                    })
                    .then(data => {
                        data.forEach(dist => {
                            const option = document.createElement('option');
                            option.value = dist.code;
                            option.textContent = dist.name;
                            districtSelect.appendChild(option);
                        });
                        districtSelect.disabled = false;
                    })
                    .catch(error => {
                        alert(error.message);
                    })
                    .finally(() => {
                        districtLoading.classList.add('hidden');
                    });

                // Load Est Delivery range from backend API
                fetch(`/api/locations/delivery-estimate/${provinceCode}`)
                    .then(response => response.json())
                    .then(data => {
                        estDeliveryText.textContent = data.text;
                        estDeliveryContainer.classList.remove('hidden');
                    })
                    .catch(e => console.error('Lỗi tải thời gian dự kiến:', e));
            });

            districtSelect.addEventListener('change', function () {
                const districtCode = this.value;
                
                // Clear and disable downstream selectors
                wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                wardSelect.disabled = true;

                if (!districtCode) return;

                // Load wards
                wardLoading.classList.remove('hidden');
                fetch(`/api/locations/districts/${districtCode}/wards`)
                    .then(response => {
                        if (!response.ok) throw new Error('Không thể tải dữ liệu phường xã.');
                        return response.json();
                    })
                    .then(data => {
                        data.forEach(w => {
                            const option = document.createElement('option');
                            option.value = w.code;
                            option.textContent = w.name;
                            wardSelect.appendChild(option);
                        });
                        wardSelect.disabled = false;
                    })
                    .catch(error => {
                        alert(error.message);
                    })
                    .finally(() => {
                        wardLoading.classList.add('hidden');
                    });
            });

            // If old values exist, trigger cascading load
            if (provinceSelect.value) {
                const provCode = provinceSelect.value;
                fetch(`/api/locations/delivery-estimate/${provCode}`)
                    .then(response => response.json())
                    .then(data => {
                        estDeliveryText.textContent = data.text;
                        estDeliveryContainer.classList.remove('hidden');
                    });
                
                // Load districts and select old
                districtSelect.disabled = false;
                fetch(`/api/locations/provinces/${provCode}/districts`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(dist => {
                            const option = document.createElement('option');
                            option.value = dist.code;
                            option.textContent = dist.name;
                            const oldDist = "{{ old('district_code', $user->district_code) }}";
                            if (dist.code == oldDist) {
                                option.selected = true;
                            }
                            districtSelect.appendChild(option);
                        });
                        
                        // If old district existed, trigger wards
                        const currentDist = districtSelect.value;
                        if (currentDist) {
                            wardSelect.disabled = false;
                            fetch(`/api/locations/districts/${currentDist}/wards`)
                                .then(response => response.json())
                                .then(data => {
                                    data.forEach(w => {
                                        const option = document.createElement('option');
                                        option.value = w.code;
                                        option.textContent = w.name;
                                        const oldWard = "{{ old('ward_code', $user->ward_code) }}";
                                        if (w.code == oldWard) {
                                            option.selected = true;
                                        }
                                        wardSelect.appendChild(option);
                                    });
                                });
                        }
                    });
            }

            // 2. Receipt E-Mail toggle visibility
            for (let radio of receiptTypeRadios) {
                radio.addEventListener('change', function () {
                    if (this.value === 'electronic') {
                        receiptEmailContainer.classList.remove('hidden');
                        receiptEmailInput.required = true;
                    } else {
                        receiptEmailContainer.classList.add('hidden');
                        receiptEmailInput.required = false;
                    }
                });
            }
            // Trigger old state
            const activeReceiptType = document.querySelector('input[name="receipt_type"]:checked').value;
            if (activeReceiptType === 'electronic') {
                receiptEmailContainer.classList.remove('hidden');
                receiptEmailInput.required = true;
            }

            // 3. Payment Option Toggle and UI display
            function updatePaymentPanels() {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
                
                // Reset styles
                paymentLabels.forEach(lbl => {
                    lbl.classList.remove('border-orange-500');
                    lbl.classList.add('border-slate-200');
                });
                paymentDetailPanels.forEach(panel => {
                    panel.classList.add('hidden');
                });

                // Style active tab
                const activeLabel = document.getElementById('label-' + selectedMethod) || document.getElementById('label-' + selectedMethod.replace(/_/g, '-'));
                if (activeLabel) {
                    activeLabel.classList.remove('border-slate-200');
                    activeLabel.classList.add('border-orange-500');
                }
                
                const activePanel = document.getElementById('panel-' + selectedMethod) || document.getElementById('panel-' + selectedMethod.replace(/_/g, '-'));
                if (activePanel) {
                    activePanel.classList.remove('hidden');
                }

                const currentTotal = parseInt(totalValEl.dataset.value);

                // If Bank Transfer, update QR code parameters
                if (selectedMethod === 'bank_transfer') {
                    const phone = receiverPhone.value.trim() || '0987654321';
                    qrAmountText.textContent = new Intl.NumberFormat('vi-VN').format(currentTotal) + ' ₫';
                    qrSyntaxText.textContent = 'GD ' + phone;
                    vietqrImg.src = `https://img.vietqr.io/image/tcb-19028374829012-compact.jpg?amount=${currentTotal}&addInfo=GD_${phone}&accountName=CONG%20TY%20TNHH%20GIADUNGPRO`;
                }

                // If Installment, update calculations
                if (selectedMethod === 'installment') {
                    const months = parseInt(installmentSelect.value);
                    const monthlyCost = Math.round(currentTotal / months);
                    monthlyInstallmentText.textContent = new Intl.NumberFormat('vi-VN').format(monthlyCost) + ' ₫ / tháng';
                }
            }

            for (let radio of paymentRadios) {
                radio.addEventListener('change', updatePaymentPanels);
            }
            receiverPhone.addEventListener('input', updatePaymentPanels);
            installmentSelect.addEventListener('change', updatePaymentPanels);

            // Run on init
            updatePaymentPanels();

            // 4. Coupon & Voucher Code AJAX apply / cancel
            window.selectVoucher = function(code) {
                couponInput.value = code;
                applyCouponBtn.click();
            };

            applyCouponBtn.addEventListener('click', function () {
                const code = couponInput.value.trim();
                const subtotal = parseFloat(subtotalValEl.dataset.value);

                if (!code) {
                    showCouponMessage('Vui lòng nhập mã giảm giá.', 'text-red-500');
                    return;
                }

                couponMessage.classList.add('hidden');
                applyCouponBtn.disabled = true;
                applyCouponBtn.textContent = 'Đang áp dụng...';

                fetch('/api/coupons/apply', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        code: code,
                        subtotal: subtotal
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Apply coupon values to View
                        couponHiddenInput.value = data.code;
                        couponInput.disabled = true;
                        applyCouponBtn.classList.add('hidden');
                        cancelCouponBtn.classList.remove('hidden');

                        // Product discount row
                        if (data.product_discount && data.product_discount > 0) {
                            discountRow.classList.remove('hidden');
                            discountVal.textContent = '-' + new Intl.NumberFormat('vi-VN').format(data.product_discount) + ' ₫';
                        } else {
                            discountRow.classList.add('hidden');
                            discountVal.textContent = '-0 ₫';
                        }

                        // Shipping discount row
                        if (data.shipping_discount && data.shipping_discount > 0) {
                            shippingDiscountRow.classList.remove('hidden');
                            shippingDiscountVal.textContent = '-' + new Intl.NumberFormat('vi-VN').format(data.shipping_discount) + ' ₫';
                        } else {
                            shippingDiscountRow.classList.add('hidden');
                            shippingDiscountVal.textContent = '-0 ₫';
                        }

                        // Shipping fee display
                        const origShip = data.original_shipping_fee;
                        if (origShip === 0) {
                            shippingValEl.textContent = 'Miễn phí';
                        } else {
                            shippingValEl.textContent = new Intl.NumberFormat('vi-VN').format(origShip) + ' ₫';
                        }

                        // Total amount
                        totalValEl.textContent = new Intl.NumberFormat('vi-VN').format(data.total_amount) + ' ₫';
                        totalValEl.dataset.value = data.total_amount;

                        showCouponMessage(data.message, 'text-emerald-600');
                        
                        // Update payment panels info
                        updatePaymentPanels();
                    } else {
                        showCouponMessage(data.message, 'text-rose-500');
                    }
                })
                .catch(e => {
                    showCouponMessage('Không thể áp dụng mã lúc này. Vui lòng thử lại.', 'text-rose-500');
                    console.error('Coupon error:', e);
                })
                .finally(() => {
                    applyCouponBtn.disabled = false;
                    applyCouponBtn.textContent = 'Áp dụng';
                });
            });

            cancelCouponBtn.addEventListener('click', function () {
                // Reset inputs
                couponInput.value = '';
                couponInput.disabled = false;
                couponHiddenInput.value = '';
                
                applyCouponBtn.classList.remove('hidden');
                cancelCouponBtn.classList.add('hidden');

                // Hide discount rows
                discountRow.classList.add('hidden');
                discountVal.textContent = '-0 ₫';
                shippingDiscountRow.classList.add('hidden');
                shippingDiscountVal.textContent = '-0 ₫';

                // Restore default pricing values
                const subtotal = parseFloat(subtotalValEl.dataset.value);
                const originalShipping = subtotal >= 500000 ? 0 : 30000;
                const originalTotal = subtotal + originalShipping;

                shippingValEl.textContent = originalShipping === 0 ? 'Miễn phí' : new Intl.NumberFormat('vi-VN').format(originalShipping) + ' ₫';
                totalValEl.textContent = new Intl.NumberFormat('vi-VN').format(originalTotal) + ' ₫';
                totalValEl.dataset.value = originalTotal;

                couponMessage.classList.add('hidden');

                // Update payments info
                updatePaymentPanels();
            });

            function showCouponMessage(text, className) {
                couponMessage.className = `text-[10px] font-bold mt-1.5 ${className}`;
                couponMessage.textContent = text;
                couponMessage.classList.remove('hidden');
            }
        });
    </script>
@endsection
