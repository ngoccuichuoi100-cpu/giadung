@extends('layouts.client')

@section('title', 'Chi tiết đơn hàng ' . $order->order_code . ' - GiaDụngPro')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-slate-500 hover:text-orange-500 flex items-center gap-1.5 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Quay lại lịch sử đặt hàng
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900">Chi tiết đơn hàng: <span class="text-orange-500">{{ $order->order_code }}</span></h1>
                <p class="text-sm text-slate-500 mt-1">Đặt lúc: {{ $order->created_at->format('H:i d/m/Y') }}</p>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center gap-3">
                @if($order->order_status === 'pending')
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-2xl shadow transition-all">
                            Hủy đơn hàng này
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Status highlights banner -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm mb-8">
            <div class="flex items-center gap-3.5">
                <span class="p-3 bg-slate-50 text-slate-600 rounded-2xl border border-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                <div>
                    <span class="text-slate-400 text-xs font-semibold block uppercase">Trạng Thái Đơn Hàng</span>
                    <span class="font-extrabold text-sm text-slate-800">
                        @if($order->order_status === 'pending') Chờ xác nhận
                        @elseif($order->order_status === 'confirmed') Đã xác nhận
                        @elseif($order->order_status === 'processing') Đang chuẩn bị hàng
                        @elseif($order->order_status === 'shipping') Đang giao hàng
                        @elseif($order->order_status === 'completed') Hoàn thành
                        @else Đã hủy
                        @endif
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3.5">
                <span class="p-3 bg-slate-50 text-slate-600 rounded-2xl border border-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </span>
                <div>
                    <span class="text-slate-400 text-xs font-semibold block uppercase">Trạng Thái Thanh Toán</span>
                    <span class="font-extrabold text-sm">
                        @if($order->payment_status === 'paid')
                            <span class="text-emerald-600">Đã thanh toán {{ $order->payment_method === 'payos' ? '(PayOS)' : '' }}</span>
                        @elseif($order->payment_status === 'refunded')
                            <span class="text-purple-600">Đã hoàn tiền</span>
                        @elseif($order->payment_status === 'failed')
                            <span class="text-red-600">Thất bại</span>
                        @else
                            <span class="text-amber-600">Chờ thanh toán {{ $order->payment_method === 'payos' ? '(PayOS)' : ($order->payment_method === 'cod' ? '(COD)' : '') }}</span>
                        @endif
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3.5">
                <span class="p-3 bg-slate-50 text-slate-600 rounded-2xl border border-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                <div>
                    <span class="text-slate-400 text-xs font-semibold block uppercase">Tổng tiền thanh toán</span>
                    <span class="font-extrabold text-sm text-orange-500">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Receiver & Delivery Details -->
            <div class="lg:col-span-1 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm h-fit space-y-6">
                <!-- Info Section -->
                <div>
                    <h3 class="font-bold text-slate-800 text-base border-b border-slate-100 pb-2.5 mb-4">Thông tin giao nhận</h3>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li>
                            <strong class="text-slate-800 block">Họ tên người nhận</strong>
                            {{ $order->receiver_name }}
                        </li>
                        <li>
                            <strong class="text-slate-800 block">Số điện thoại</strong>
                            {{ $order->receiver_phone }}
                        </li>
                        <li>
                            <strong class="text-slate-800 block">Email</strong>
                            {{ $order->receiver_email }}
                        </li>
                        <li>
                            <strong class="text-slate-800 block">Địa chỉ nhận hàng</strong>
                            {{ $order->address }}, {{ $order->ward }}, {{ $order->district }}, {{ $order->province }}
                        </li>
                        @if($order->est_delivery_start && $order->est_delivery_end)
                            <li class="p-3 bg-orange-50/60 border border-orange-100 rounded-2xl">
                                <strong class="text-orange-700 block text-xs uppercase tracking-wider mb-0.5">Dự kiến nhận hàng</strong>
                                <span class="text-xs text-orange-950 font-semibold">
                                    Từ ngày {{ $order->est_delivery_start->format('d/m/Y') }} đến ngày {{ $order->est_delivery_end->format('d/m/Y') }}
                                </span>
                            </li>
                        @endif
                        @if($order->note)
                            <li>
                                <strong class="text-slate-800 block">Ghi chú từ khách</strong>
                                {{ $order->note }}
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- Receipt details -->
                <div>
                    <h3 class="font-bold text-slate-800 text-base border-b border-slate-100 pb-2.5 mb-4">Biên lai / Hóa đơn</h3>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li>
                            <strong class="text-slate-800 block">Hình thức nhận</strong>
                            @if($order->receipt_type === 'electronic')
                                <span class="inline-flex items-center gap-1.5 text-emerald-600 font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Biên lai điện tử (E-Receipt)
                                </span>
                            @else
                                Biên lai giấy gửi kèm hộp hàng
                            @endif
                        </li>
                        @if($order->receipt_type === 'electronic')
                            <li>
                                <strong class="text-slate-800 block">Email nhận hóa đơn</strong>
                                <span class="font-semibold text-slate-900 font-mono">{{ $order->receipt_email ?: $order->receiver_email }}</span>
                            </li>
                            @if($order->payment_status === 'paid')
                                <li>
                                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl space-y-2">
                                        <div class="text-xs text-emerald-700">
                                            @if($order->receipt_sent_at)
                                                ✓ Đã gửi biên lai điện tử vào hòm thư lúc <strong>{{ $order->receipt_sent_at->format('d/m/Y H:i') }}</strong>.
                                            @else
                                                ✓ Đơn hàng đã thanh toán thành công.
                                            @endif
                                        </div>
                                        <form action="{{ route('orders.resend-receipt', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs font-bold text-orange-600 hover:text-orange-700 underline flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                Gửi lại biên lai điện tử vào email này
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            @else
                                <li class="text-xs text-slate-400 italic">
                                    Biên lai điện tử sẽ được hệ thống tự động gửi tới email của bạn ngay sau khi hoàn tất thanh toán.
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>

                <!-- Payment details -->
                <div>
                    <h3 class="font-bold text-slate-800 text-base border-b border-slate-100 pb-2.5 mb-4">Thông tin thanh toán</h3>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li>
                            <strong class="text-slate-800 block">Phương thức</strong>
                            @if($order->payment_method === 'payos')
                                <span class="font-bold text-blue-600 flex items-center gap-1">
                                    Thanh toán tự động PayOS (VietQR)
                                </span>
                            @elseif($order->payment_method === 'cod')
                                Thanh toán khi nhận hàng (COD)
                            @elseif($order->payment_method === 'bank_transfer')
                                Chuyển khoản ngân hàng (VietQR)
                            @elseif($order->payment_method === 'installment')
                                Trả góp kỳ hạn linh hoạt
                            @else
                                {{ strtoupper($order->payment_method) }}
                            @endif
                        </li>
                        @if($order->payment_method === 'installment')
                            <li class="p-3 bg-slate-50 border border-slate-200 rounded-2xl">
                                <strong class="text-slate-800 block text-xs mb-0.5">Kế hoạch trả góp:</strong>
                                <p class="text-xs">Kỳ hạn: <span class="font-bold text-slate-900">{{ $order->installment_months }} tháng</span></p>
                                <p class="text-xs">Mỗi tháng trả: <strong class="text-orange-500 font-extrabold">{{ number_format($order->installment_monthly_amount, 0, ',', '.') }} ₫ / tháng</strong></p>
                            </li>
                        @endif

                        <!-- Show PayOS Payment Card if method is payos and status is pending -->
                        @if($order->payment_method === 'payos' && $order->payment_status === 'pending')
                            <li class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50/50 border border-blue-200 rounded-2xl space-y-3 text-xs">
                                <div class="flex items-center justify-between">
                                    <strong class="text-blue-900 font-bold text-sm flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        Cổng thanh toán PayOS
                                    </strong>
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-extrabold rounded-full animate-pulse">Chờ thanh toán</span>
                                </div>
                                <p class="text-slate-600 text-[11px] leading-relaxed">
                                    Đơn hàng của bạn đang chờ thanh toán qua PayOS. Bạn có thể bấm nút bên dưới để chuyển tiếp tới cổng thanh toán VietQR bảo mật.
                                </p>
                                <div class="space-y-2 pt-1">
                                    <a href="{{ route('payos.checkout', $order->id) }}" class="w-full py-2.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs rounded-xl shadow transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        Thanh toán ngay bằng PayOS
                                    </a>
                                    <button type="button" id="btn-check-payos" class="w-full py-2 px-3 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs rounded-xl border border-slate-200 transition-all flex items-center justify-center gap-1.5 shadow-xs">
                                        <svg id="check-spinner" class="w-3.5 h-3.5 text-blue-600 hidden animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                                        <span>Kiểm tra trạng thái giao dịch</span>
                                    </button>
                                </div>
                                <div id="payos-check-result" class="text-[11px] font-medium text-center hidden"></div>
                            </li>
                        @endif

                        @if($order->payment_method === 'payos' && $order->payment_status === 'paid')
                            <li class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl text-xs space-y-1">
                                <div class="flex items-center gap-2 text-emerald-800 font-bold text-sm">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Đã thanh toán tự động qua PayOS
                                </div>
                                <p class="text-slate-600 text-[11px]">
                                    Mã tham chiếu: <strong class="text-slate-900">{{ $order->payment ? $order->payment->transaction_reference : ('PAYOS-' . $order->payos_order_code) }}</strong>
                                </p>
                            </li>
                        @endif
                        
                        <!-- Show Bank Transfer Guide if method is bank_transfer and status is pending -->
                        @if($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
                            <li class="p-4 bg-orange-50/60 border border-orange-100 rounded-2xl space-y-3 text-xs">
                                <strong class="text-orange-800 block font-bold text-sm">Hướng dẫn chuyển khoản VietQR:</strong>
                                <div class="w-32 h-32 mx-auto bg-white border border-slate-200 rounded-lg overflow-hidden p-1 flex items-center justify-center">
                                    <img src="https://img.vietqr.io/image/tcb-19028374829012-compact.jpg?amount={{ (int)$order->total_amount }}&addInfo=GD_{{ $order->receiver_phone }}&accountName=CONG%20TY%20TNHH%20GIADUNGPRO" class="w-full h-full object-contain">
                                </div>
                                <div class="space-y-1 text-[11px] text-slate-700 leading-relaxed">
                                    <p>Số tài khoản: <strong>19028374829012</strong></p>
                                    <p>Ngân hàng: <strong>Techcombank</strong></p>
                                    <p>Chủ tài khoản: <strong>CONG TY TNHH GIADUNGPRO</strong></p>
                                    <p>Số tiền: <strong class="text-orange-500">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</strong></p>
                                    <p>Nội dung CK: <strong class="text-slate-900 px-1 py-0.5 bg-white border border-slate-200 rounded">GD_{{ $order->receiver_phone }}</strong></p>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Right: Order Items Products Confirmation -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-55 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase">
                                <th class="p-6">Sản phẩm</th>
                                <th class="p-6 text-center">Đơn giá</th>
                                <th class="p-6 text-center">Số lượng</th>
                                <th class="p-6 text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                            @foreach($order->items as $item)
                                <tr>
                                    <!-- Product basic -->
                                    <td class="p-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-slate-50 border border-slate-200 rounded-xl overflow-hidden flex-shrink-0">
                                                @if($item->product_image)
                                                    <img src="{{ $item->product_image_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-800 text-xs">{{ $item->product_name }}</h4>
                                                <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                                    <span class="text-slate-400 text-[10px] uppercase font-semibold">Mã: {{ $item->product_code }}</span>
                                                    @if($item->color)
                                                        <span class="inline-flex items-center gap-1 text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-medium">
                                                            <span class="w-1.5 h-1.5 rounded-full inline-block border border-slate-300" style="background-color: {{ $item->color->hex_code ?? '#ccc' }}"></span>
                                                            {{ $item->color->name }}
                                                        </span>
                                                    @endif
                                                    @if($item->size)
                                                        <span class="inline-flex items-center text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-medium">
                                                            Size: {{ $item->size->name }}
                                                        </span>
                                                    @endif
                                                    @if($item->warranty)
                                                        <span class="inline-flex items-center text-[10px] bg-amber-50 text-amber-700 border border-amber-200 px-1.5 py-0.5 rounded font-medium">
                                                            BH: {{ $item->warranty->name }} ({{ $item->warranty->duration_months }}T)
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Price -->
                                    <td class="p-6 text-center font-semibold">
                                        {{ number_format($item->price, 0, ',', '.') }} ₫
                                    </td>

                                    <!-- Quantity -->
                                    <td class="p-6 text-center font-bold">
                                        {{ $item->quantity }}
                                    </td>

                                    <!-- Total -->
                                    <td class="p-6 text-right font-extrabold text-slate-900">
                                        {{ number_format($item->total, 0, ',', '.') }} ₫
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="bg-slate-50 p-6 border-t border-slate-100 space-y-3.5 text-sm text-slate-600">
                        <div class="flex justify-between">
                            <span>Tạm tính</span>
                            <span class="font-bold text-slate-800">{{ number_format($order->subtotal, 0, ',', '.') }} ₫</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-rose-600">
                                <span>Giảm giá sản phẩm @if($order->coupon_code && (!$order->shipping_discount || $order->shipping_discount <= 0)) (Mã: {{ $order->coupon_code }}) @endif</span>
                                <span class="font-bold">-{{ number_format($order->discount_amount, 0, ',', '.') }} ₫</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span>Phí vận chuyển</span>
                            <span class="font-bold text-slate-800">
                                @if($order->shipping_fee == 0)
                                    Miễn phí
                                @else
                                    {{ number_format($order->shipping_fee, 0, ',', '.') }} ₫
                                @endif
                            </span>
                        </div>
                        @if($order->shipping_discount > 0)
                            <div class="flex justify-between text-emerald-600">
                                <span>Giảm phí vận chuyển @if($order->coupon_code) (Mã: {{ $order->coupon_code }}) @endif</span>
                                <span class="font-bold">-{{ number_format($order->shipping_discount, 0, ',', '.') }} ₫</span>
                            </div>
                        @endif
                        <div class="border-t border-slate-200 pt-3.5 flex justify-between text-base font-extrabold text-slate-900">
                            <span>Tổng cộng</span>
                            <span class="text-lg text-orange-500">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($order->payment_method === 'payos' && $order->payment_status === 'pending')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btnCheck = document.getElementById('btn-check-payos');
            const spinner = document.getElementById('check-spinner');
            const resultBox = document.getElementById('payos-check-result');
            let isPolling = true;

            function checkPaymentStatus(isManual = false) {
                if (isManual) {
                    if (btnCheck) btnCheck.disabled = true;
                    if (spinner) spinner.classList.remove('hidden');
                    if (resultBox) {
                        resultBox.textContent = 'Đang kiểm tra giao dịch...';
                        resultBox.className = 'text-[11px] font-medium text-slate-500 text-center block mt-1';
                    }
                }

                fetch("{{ route('orders.payos-status', $order->id) }}")
                    .then(res => res.json())
                    .then(data => {
                        if (data.paid) {
                            isPolling = false;
                            if (resultBox) {
                                resultBox.textContent = 'Thanh toán thành công! Đang tải lại trang...';
                                resultBox.className = 'text-[11px] font-bold text-emerald-600 text-center block mt-1';
                            }
                            setTimeout(() => {
                                window.location.reload();
                            }, 1200);
                        } else if (isManual) {
                            if (resultBox) {
                                resultBox.textContent = data.message || 'Chưa nhận được thanh toán. Vui lòng thử lại sau khi hoàn tất quét mã.';
                                resultBox.className = 'text-[11px] font-medium text-amber-600 text-center block mt-1';
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Lỗi kiểm tra PayOS:', err);
                        if (isManual && resultBox) {
                            resultBox.textContent = 'Không thể kiểm tra lúc này. Vui lòng thử lại sau.';
                            resultBox.className = 'text-[11px] font-medium text-red-500 text-center block mt-1';
                        }
                    })
                    .finally(() => {
                        if (isManual) {
                            if (btnCheck) btnCheck.disabled = false;
                            if (spinner) spinner.classList.add('hidden');
                        }
                    });
            }

            if (btnCheck) {
                btnCheck.addEventListener('click', function () {
                    checkPaymentStatus(true);
                });
            }

            // Auto-poll every 5 seconds
            let pollCount = 0;
            const pollInterval = setInterval(() => {
                if (!isPolling || pollCount >= 60) {
                    clearInterval(pollInterval);
                    return;
                }
                pollCount++;
                checkPaymentStatus(false);
            }, 5000);
        });
    </script>
    @endif
@endsection
