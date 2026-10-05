@extends('layouts.admin')

@section('title', 'Đơn hàng ' . $order->order_code . ' - GiaDụngPro')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-slate-500 hover:text-orange-500 flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Quay lại danh sách đơn hàng
                </a>
                <h2 class="font-bold text-slate-800 text-lg">Chi tiết đơn hàng: {{ $order->order_code }}</h2>
                <p class="text-xs text-slate-400 mt-0.5">Đặt ngày: {{ $order->created_at->format('H:i d/m/Y') }}</p>
            </div>

            <!-- Status Badges Highlight -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Payment Status Badge -->
                @if($order->payment_status === 'paid')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-200 shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Đã thanh toán
                    </span>
                @elseif($order->payment_status === 'failed')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Thất bại
                    </span>
                @elseif($order->payment_status === 'refunded')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-xl border border-purple-200 shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                        Đã hoàn tiền
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-xl border border-amber-200 shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Chưa thanh toán
                    </span>
                @endif

                <!-- Order Status Badge -->
                @if($order->order_status === 'completed')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-200 shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l5-5z" clip-rule="evenodd"></path></svg>
                        Hoàn thành
                    </span>
                @elseif($order->order_status === 'cancelled')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 text-rose-700 text-xs font-bold rounded-xl border border-rose-200 shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Đã hủy đơn {{ $order->cancelled_at ? '(' . $order->cancelled_at->format('H:i d/m') . ')' : '' }}
                    </span>
                @elseif($order->order_status === 'pending')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 text-xs font-bold rounded-xl border border-amber-200 shadow-2xs animate-pulse">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Chờ xác nhận
                    </span>
                @elseif($order->order_status === 'confirmed')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-50 text-sky-700 text-xs font-bold rounded-xl border border-sky-200 shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Đã xác nhận
                    </span>
                @elseif($order->order_status === 'processing')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-xl border border-indigo-200 shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Đang chuẩn bị
                    </span>
                @elseif($order->order_status === 'shipping')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-xl border border-blue-200 shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                        Đang giao hàng
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Panel: Delivery information & Update status form -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Status Update Form -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                    <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2 mb-4">Cập nhật trạng thái</h3>
                    
                    <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Order Status -->
                        <div>
                            <label for="order_status" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Trạng thái đơn hàng</label>
                            <select id="order_status" name="order_status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white"
                                {{ $order->order_status === 'cancelled' || $order->order_status === 'completed' ? 'disabled' : '' }}>
                                <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="confirmed" {{ $order->order_status === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Đang chuẩn bị hàng</option>
                                <option value="shipping" {{ $order->order_status === 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                                <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>

                        <!-- Payment Status -->
                        <div>
                            <label for="payment_status" class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Trạng thái thanh toán</label>
                            <select id="payment_status" name="payment_status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white"
                                {{ $order->order_status === 'cancelled' ? 'disabled' : '' }}>
                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Thanh toán thất bại</option>
                                <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                            </select>
                        </div>

                        @if($order->order_status !== 'cancelled' && $order->order_status !== 'completed')
                            <button type="submit" class="w-full py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                                Lưu cập nhật
                            </button>
                        @else
                            <p class="text-[10px] text-slate-400 italic text-center">Đơn hàng ở trạng thái cuối (Đã hủy/Hoàn thành), không thể cập nhật thêm.</p>
                        @endif
                    </form>
                </div>

                <!-- Customer Details Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
                    <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2 mb-4">Người mua hàng</h3>
                    <ul class="space-y-2.5 text-xs text-slate-655 text-slate-700">
                        <li>
                            <strong class="text-slate-800 block">Tên tài khoản:</strong>
                            {{ $order->user->name }}
                        </li>
                        <li>
                            <strong class="text-slate-800 block">Email đăng ký:</strong>
                            {{ $order->user->email }}
                        </li>
                        <li>
                            <strong class="text-slate-800 block">Số điện thoại liên hệ:</strong>
                            {{ $order->user->phone ?? 'Chưa cập nhật' }}
                        </li>
                    </ul>
                </div>

                <!-- Delivery Details Card -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2 mb-3">Địa chỉ giao hàng</h3>
                        <ul class="space-y-2 text-xs text-slate-700">
                            <li><strong>Người nhận:</strong> {{ $order->receiver_name }}</li>
                            <li><strong>Số điện thoại:</strong> {{ $order->receiver_phone }}</li>
                            <li><strong>Địa chỉ:</strong> {{ $order->address }}, {{ $order->ward }}, {{ $order->district }}, {{ $order->province }}</li>
                            @if($order->est_delivery_start && $order->est_delivery_end)
                                <li class="p-2 bg-orange-50 border border-orange-100 rounded-lg text-[11px]">
                                    <span class="text-orange-700 font-bold block uppercase tracking-wider text-[9px] mb-0.5">Dự kiến nhận hàng:</span>
                                    Từ ngày {{ $order->est_delivery_start->format('d/m/Y') }} đến ngày {{ $order->est_delivery_end->format('d/m/Y') }}
                                </li>
                            @endif
                            @if($order->note)
                                <li>
                                    <strong>Ghi chú:</strong>
                                    <span class="text-slate-500 bg-slate-50 p-1.5 rounded block border border-slate-100 mt-1 italic">{{ $order->note }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <!-- Receipt options -->
                    <div class="border-t border-slate-100 pt-3">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-bold text-slate-800 text-sm">Biên lai / Hóa đơn</h3>
                            @if($order->payment_status === 'paid')
                                <form action="{{ route('admin.orders.send-receipt', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có muốn gửi hóa đơn điện tử tới email {{ $order->receipt_email ?: $order->receiver_email }}?');">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 transition-all flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        {{ $order->receipt_sent_at ? 'Gửi lại biên lai' : 'Gửi biên lai qua email' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                        <p class="text-xs text-slate-700">
                            Hình thức: 
                            <strong class="text-slate-900">
                                @if($order->receipt_type === 'electronic')
                                    <span class="inline-flex items-center gap-1 text-emerald-600 font-bold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Biên lai điện tử (E-Receipt)
                                    </span>
                                @else
                                    Biên lai giấy (Gửi cùng hàng)
                                @endif
                            </strong>
                        </p>
                        @if($order->receipt_type === 'electronic')
                            <p class="text-xs text-slate-700 mt-1">Email nhận: <strong class="text-slate-900 font-mono">{{ $order->receipt_email ?: $order->receiver_email }}</strong></p>
                            @if($order->receipt_sent_at)
                                <p class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center gap-1">
                                    ✓ Đã gửi lúc: {{ $order->receipt_sent_at->format('d/m/Y H:i') }}
                                </p>
                            @elseif($order->payment_status === 'paid')
                                <p class="text-[11px] text-amber-600 font-semibold mt-1">
                                    ⏳ Đang chờ gửi email biên lai
                                </p>
                            @else
                                <p class="text-[11px] text-slate-400 mt-1 italic">
                                    (Hóa đơn sẽ tự động gửi qua email ngay khi đơn hàng chuyển sang trạng thái "Đã thanh toán")
                                </p>
                            @endif
                        @endif
                    </div>

                    <!-- Payment details -->
                    <div class="border-t border-slate-100 pt-3">
                        <h3 class="font-bold text-slate-800 text-sm mb-2">Phương thức thanh toán</h3>
                        <p class="text-xs text-slate-700">
                            Phương thức: <strong class="text-slate-900">
                                @if($order->payment_method === 'payos')
                                    <span class="text-blue-600 font-bold">Thanh toán tự động PayOS</span>
                                @elseif($order->payment_method === 'cod')
                                    Thanh toán khi nhận hàng (COD)
                                @elseif($order->payment_method === 'bank_transfer')
                                    Chuyển khoản VietQR
                                @elseif($order->payment_method === 'installment')
                                    Trả góp 0%
                                @else
                                    {{ strtoupper($order->payment_method) }}
                                @endif
                            </strong>
                        </p>
                        @if($order->payment_method === 'payos')
                            <div class="mt-2 p-2.5 bg-blue-50/70 border border-blue-100 rounded-lg text-xs space-y-1">
                                <p>Cổng: <strong class="text-blue-700">PayOS VietQR</strong></p>
                                @if($order->payos_order_code)
                                    <p>Mã đơn PayOS: <strong class="text-slate-800">{{ $order->payos_order_code }}</strong></p>
                                @endif
                                @if($order->payment && $order->payment->transaction_reference)
                                    <p>Mã GD ngân hàng: <strong class="text-emerald-700">{{ $order->payment->transaction_reference }}</strong></p>
                                @endif
                            </div>
                        @endif
                        @if($order->payment_method === 'installment')
                            <div class="mt-2 p-2 bg-slate-50 border border-slate-100 rounded-lg text-xs">
                                <p>Kỳ hạn: <strong>{{ $order->installment_months }} tháng</strong></p>
                                <p>Mỗi tháng: <strong class="text-orange-500 font-extrabold">{{ number_format($order->installment_monthly_amount, 0, ',', '.') }} ₫</strong></p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Panel: Products & Pricing calculation -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                    <table class="w-full text-left border-collapse text-xs text-slate-755 text-slate-800">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase">
                                <th class="p-4">Sản phẩm</th>
                                <th class="p-4 text-center">Đơn giá</th>
                                <th class="p-4 text-center">Số lượng</th>
                                <th class="p-4 text-right">Tổng cộng</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($order->items as $item)
                                <tr>
                                    <!-- Product details -->
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-slate-50 border border-slate-200 rounded-lg overflow-hidden flex-shrink-0">
                                                @if($item->product_image)
                                                    <img src="{{ $item->product_image_url }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-350">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-800">{{ $item->product_name }}</h4>
                                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                                    <span class="text-[10px] text-slate-400 font-semibold uppercase">Mã: {{ $item->product_code }}</span>
                                                    @if($item->color)
                                                        <span class="inline-flex items-center gap-1 text-[11px] bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-medium">
                                                            <span class="w-2 h-2 rounded-full inline-block border border-slate-300" style="background-color: {{ $item->color->hex_code ?? '#ccc' }}"></span>
                                                            Màu: {{ $item->color->name }}
                                                        </span>
                                                    @endif
                                                    @if($item->size)
                                                        <span class="inline-flex items-center text-[11px] bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-medium">
                                                            Size: {{ $item->size->name }}
                                                        </span>
                                                    @endif
                                                    @if($item->warranty)
                                                        <span class="inline-flex items-center text-[11px] bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded font-medium">
                                                            BH: {{ $item->warranty->name }} ({{ $item->warranty->duration_months }} tháng)
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Price -->
                                    <td class="p-4 text-center font-semibold">
                                        {{ number_format($item->price, 0, ',', '.') }} ₫
                                    </td>

                                    <!-- Qty -->
                                    <td class="p-4 text-center font-bold">
                                        {{ $item->quantity }}
                                    </td>

                                    <!-- Item Total -->
                                    <td class="p-4 text-right font-extrabold text-slate-900">
                                        {{ number_format($item->total, 0, ',', '.') }} ₫
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Price computation panel -->
                    <div class="bg-slate-50 p-6 border-t border-slate-100 space-y-3.5 text-xs text-slate-600">
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
                        <div class="border-t border-slate-200 pt-3.5 flex justify-between text-sm font-extrabold text-slate-900">
                            <span>Tổng tiền đơn</span>
                            <span class="text-base text-orange-500">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
