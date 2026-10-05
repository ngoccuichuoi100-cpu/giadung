@extends('layouts.admin')

@section('title', 'Khách hàng ' . $customer->name . ' - GiaDụngPro')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <a href="{{ route('admin.customers.index') }}" class="text-xs font-semibold text-slate-500 hover:text-orange-500 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại danh sách khách hàng
            </a>
            <h2 class="font-bold text-slate-800 text-lg">Hồ sơ khách hàng: {{ $customer->name }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">Quản lý tài khoản khách và xem lịch sử giao dịch mua sắm</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Personal info summary card -->
            <div class="lg:col-span-1 bg-white border border-slate-200 rounded-2xl p-6 shadow-xs h-fit space-y-6">
                <div class="text-center space-y-3">
                    <div class="w-16 h-16 bg-orange-100 text-orange-655 text-orange-600 rounded-full flex items-center justify-center font-bold text-xl mx-auto shadow-inner">
                        {{ substr($customer->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">{{ $customer->name }}</h3>
                        <p class="text-slate-400 text-xs mt-0.5">{{ $customer->email }}</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4 space-y-3 text-xs text-slate-655 text-slate-700">
                    <div>
                        <strong class="text-slate-800 block">Số điện thoại:</strong>
                        {{ $customer->phone ?? 'Chưa cập nhật' }}
                    </div>
                    <div>
                        <strong class="text-slate-800 block">Địa chỉ đăng ký:</strong>
                        {{ $customer->address ?? 'Chưa cập nhật' }}
                    </div>
                    <div>
                        <strong class="text-slate-800 block">Trạng thái tài khoản:</strong>
                        @if($customer->status === 'active')
                            <span class="px-2 py-0.5 text-[10px] rounded bg-green-50 text-green-700 font-bold border border-green-200">Kích hoạt</span>
                        @else
                            <span class="px-2 py-0.5 text-[10px] rounded bg-red-50 text-red-700 font-bold border border-red-200">Đã khóa</span>
                        @endif
                    </div>
                    <div>
                        <strong class="text-slate-800 block">Mã khách hàng (ID):</strong>
                        <span class="font-mono font-bold text-slate-600">#{{ $customer->id }}</span>
                    </div>
                    <div>
                        <strong class="text-slate-800 block">Ngày tham gia hệ thống:</strong>
                        {{ $customer->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>

                <!-- Account status toggle actions -->
                <div class="border-t border-slate-100 pt-4 space-y-2.5">
                    <form action="{{ route('admin.customers.toggle-status', $customer->id) }}" method="POST" onsubmit="return confirm('Xác nhận thay đổi trạng thái tài khoản của khách hàng này?');">
                        @csrf
                        @method('PATCH')
                        @if($customer->status === 'active')
                            <button type="submit" class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition-all shadow-xs text-center">
                                Khóa tài khoản khách hàng
                            </button>
                        @else
                            <button type="submit" class="w-full py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-xl transition-all shadow-xs text-center">
                                Kích hoạt tài khoản khách hàng
                            </button>
                        @endif
                    </form>

                    <!-- Xóa tài khoản vĩnh viễn -->
                    <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn XÓA VĨNH VIỄN khách hàng #{{ $customer->id }} ({{ $customer->name ?? $customer->username }})?\n\nHệ thống sẽ xóa tài khoản và tự động sắp xếp lại thứ tự ID trong CSDL.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 text-xs font-bold rounded-xl transition-all border border-rose-200 text-center flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            Xóa vĩnh viễn khách hàng
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Order transactions history table list -->
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-4">
                <h3 class="font-bold text-slate-800 text-base border-b border-slate-100 pb-3">Lịch sử đơn hàng đã đặt</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs text-slate-700">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-550 uppercase">
                                <th class="p-3">Mã đơn</th>
                                <th class="p-3">Ngày đặt</th>
                                <th class="p-3 text-right">Tổng tiền</th>
                                <th class="p-3 text-center">Thanh toán</th>
                                <th class="p-3 text-center">Trạng thái</th>
                                <th class="p-3 text-center">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($customer->orders as $order)
                                <tr>
                                    <td class="p-3 font-bold text-slate-900">{{ $order->order_code }}</td>
                                    <td class="p-3 text-slate-500">{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td class="p-3 text-right font-bold">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</td>
                                    <td class="p-3 text-center">
                                        @if($order->payment_status === 'paid')
                                            <span class="px-2 py-0.5 rounded-full bg-green-50 text-green-700 font-bold border border-green-200">Đã trả</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold border border-amber-200">Chưa</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center">
                                        @if($order->order_status === 'completed')
                                            <span class="px-2 py-0.5 rounded bg-green-50 text-green-700 font-bold border border-green-200">Hoàn thành</span>
                                        @elseif($order->order_status === 'cancelled')
                                            <span class="px-2 py-0.5 rounded bg-red-50 text-red-700 font-bold border border-red-200">Đã hủy</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold border border-slate-200">
                                                @if($order->order_status === 'pending') Chờ duyệt
                                                @elseif($order->order_status === 'confirmed') Đã nhận
                                                @elseif($order->order_status === 'processing') Chuẩn bị
                                                @elseif($order->order_status === 'shipping') Giao hàng
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-center">
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-orange-500 hover:underline">Xem</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
