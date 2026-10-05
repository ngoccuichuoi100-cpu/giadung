@extends('layouts.client')

@section('title', 'Lịch sử đơn hàng - GiaDụngPro')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-8">Lịch sử đơn hàng của bạn</h1>

        @if($orders->isEmpty())
            <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center shadow-sm">
                <svg class="w-16 h-16 text-slate-350 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="font-bold text-slate-800 text-lg mb-1">Bạn chưa đặt đơn hàng nào</h3>
                <p class="text-sm text-slate-500 mb-6">Lịch sử các đơn hàng bạn đã đặt sẽ hiển thị ở đây.</p>
                <a href="{{ route('products.index') }}" class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-full shadow transition-all">
                    Mua sắm ngay
                </a>
            </div>
        @else
            <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-55 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase">
                            <th class="p-6">Mã Đơn Hàng</th>
                            <th class="p-6">Ngày Đặt</th>
                            <th class="p-6">Người Nhận</th>
                            <th class="p-6 text-right">Tổng Tiền</th>
                            <th class="p-6 text-center">Thanh Toán</th>
                            <th class="p-6 text-center">Trạng Thái Đơn</th>
                            <th class="p-6 text-center">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        @foreach($orders as $order)
                            <tr>
                                <!-- Code -->
                                <td class="p-6 font-bold text-slate-900">
                                    <a href="{{ route('orders.show', $order->id) }}" class="hover:text-orange-500">{{ $order->order_code }}</a>
                                </td>
                                <!-- Date -->
                                <td class="p-6 text-slate-500">
                                    {{ $order->created_at->format('H:i d/m/Y') }}
                                </td>
                                <!-- Receiver Name -->
                                <td class="p-6 font-medium">
                                    {{ $order->receiver_name }}
                                </td>
                                <!-- Total amount -->
                                <td class="p-6 text-right font-bold text-slate-900">
                                    {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                                </td>
                                <!-- Payment status label -->
                                <td class="p-6 text-center">
                                    @if($order->payment_status === 'paid')
                                        <span class="inline-flex px-2.5 py-1 text-xs font-bold bg-green-50 text-green-700 rounded-full border border-green-200">Đã thanh toán {{ $order->payment_method === 'payos' ? '(PayOS)' : '' }}</span>
                                    @elseif($order->payment_status === 'refunded')
                                        <span class="inline-flex px-2.5 py-1 text-xs font-bold bg-purple-50 text-purple-700 rounded-full border border-purple-200">Đã hoàn tiền</span>
                                    @elseif($order->payment_status === 'failed')
                                        <span class="inline-flex px-2.5 py-1 text-xs font-bold bg-red-50 text-red-700 rounded-full border border-red-200">Thất bại</span>
                                    @else
                                        <span class="inline-flex px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-700 rounded-full border border-amber-200">Chưa thanh toán {{ $order->payment_method === 'payos' ? '(PayOS)' : '' }}</span>
                                    @endif
                                </td>
                                <!-- Order status label -->
                                <td class="p-6 text-center">
                                    @if($order->order_status === 'pending')
                                        <span class="inline-flex px-2.5 py-1 text-xs font-bold bg-slate-100 text-slate-700 rounded-full border border-slate-200">Chờ xác nhận</span>
                                    @elseif($order->order_status === 'confirmed')
                                        <span class="inline-flex px-2.5 py-1 text-xs font-bold bg-blue-50 text-blue-700 rounded-full border border-blue-200">Đã xác nhận</span>
                                    @elseif($order->order_status === 'processing')
                                        <span class="inline-flex px-2.5 py-1 text-xs font-bold bg-indigo-50 text-indigo-700 rounded-full border border-indigo-200">Đang chuẩn bị</span>
                                    @elseif($order->order_status === 'shipping')
                                        <span class="inline-flex px-2.5 py-1 text-xs font-bold bg-amber-50 text-amber-755 text-amber-800 rounded-full border border-amber-200">Đang giao hàng</span>
                                    @elseif($order->order_status === 'completed')
                                        <span class="inline-flex px-2.5 py-1 text-xs font-bold bg-green-50 text-green-700 rounded-full border border-green-200">Hoàn thành</span>
                                    @else
                                        <span class="inline-flex px-2.5 py-1 text-xs font-bold bg-red-50 text-red-700 rounded-full border border-red-200">Đã hủy</span>
                                    @endif
                                </td>
                                <!-- Action -->
                                <td class="p-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if($order->payment_method === 'payos' && $order->payment_status === 'pending' && $order->order_status !== 'cancelled')
                                            <a href="{{ route('payos.checkout', $order->id) }}" class="text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white py-1.5 px-3 rounded-lg shadow-xs transition-all flex items-center gap-1">
                                                <span>PayOS</span>
                                            </a>
                                        @endif

                                        <a href="{{ route('orders.show', $order->id) }}" class="text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 py-1.5 px-3 rounded-lg transition-all">
                                            Xem chi tiết
                                        </a>

                                        @if($order->order_status === 'pending')
                                            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                                                @csrf
                                                <button type="submit" class="text-xs font-bold bg-red-50 hover:bg-red-100 text-red-600 py-1.5 px-3 rounded-lg transition-all">
                                                    Hủy đơn
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
