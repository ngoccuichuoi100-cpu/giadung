@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng - GiaDụngPro')

@section('content')
    <div id="admin-panel-wrapper" class="mt-6 bg-white border border-slate-200 rounded-3xl shadow-xs text-left transition-all duration-300 relative">
        <!-- Sticky Header & Filter Bar (Chuyển đổi từ cố định sang chuyển động theo cuộn chuột) -->
        <div id="admin-sticky-bar" class="sticky top-0 z-20 bg-white border-b border-slate-100 p-6 rounded-t-3xl transition-all duration-300">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-bold text-slate-800 text-lg">Danh sách đơn hàng</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Theo dõi trạng thái giao hàng, thanh toán và xử lý đơn</p>
                </div>
                <!-- Scroll to top button (Tự động hiển thị khi navbar chuyển động) -->
                <button type="button" id="sticky-scroll-top-btn" class="opacity-0 pointer-events-none scale-90 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center gap-1.5 shadow-2xs self-end sm:self-auto" title="Cuộn lên đầu trang">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    <span class="hidden md:inline">Lên đầu</span>
                </button>
            </div>

            <!-- Filters bar -->
            <form action="{{ route('admin.orders.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100 mt-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm mã đơn, tên, số điện thoại..."
                        autocomplete="off"
                        class="w-full pl-9 pr-9 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 transition-all">
                    <div class="absolute left-3 top-2.5 text-slate-400 pointer-events-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <div class="admin-search-right-controls absolute right-2.5 top-2.5 flex items-center gap-1.5 z-10">
                        <svg class="admin-search-spinner w-3.5 h-3.5 text-orange-500 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <button type="button" class="admin-search-clear text-slate-400 hover:text-slate-600 focus:outline-none {{ request('q') ? '' : 'hidden' }}" title="Xóa tìm kiếm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                        <option value="">Tất cả trạng thái đơn</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Đang chuẩn bị hàng</option>
                        <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>

                <!-- Action buttons -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-grow py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-all shadow-xs">
                        Lọc đơn hàng
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="py-2 px-4 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-lg transition-all text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Orders Table & Pagination Container -->
        <div id="adminTableContainer" class="p-6 space-y-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs text-slate-700">
                    <thead class="bg-slate-50/95 backdrop-blur-xs shadow-2xs">
                        <tr class="border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase">
                            <th class="p-4 whitespace-nowrap">Mã đơn hàng</th>
                            <th class="p-4 whitespace-nowrap">Ngày đặt</th>
                            <th class="p-4 whitespace-nowrap">Khách hàng</th>
                            <th class="p-4 whitespace-nowrap">Người nhận</th>
                            <th class="p-4 text-right whitespace-nowrap">Tổng thanh toán</th>
                            <th class="p-4 text-center whitespace-nowrap w-24">Thanh toán</th>
                            <th class="p-4 text-center whitespace-nowrap w-24">Trạng thái đơn</th>
                            <th class="p-4 text-center whitespace-nowrap w-20">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($orders as $order)
                            <tr class="hover:bg-slate-50/50">
                                <!-- Code -->
                                <td class="p-4 font-bold text-slate-900 whitespace-nowrap">{{ $order->order_code }}</td>
                                <!-- Date -->
                                <td class="p-4 text-slate-400 whitespace-nowrap">{{ $order->created_at->format('H:i d/m/Y') }}</td>
                                <!-- Customer -->
                                <td class="p-4">
                                    <div class="font-bold text-slate-800 truncate max-w-[150px]" title="{{ $order->user->name ?? 'Khách vãng lai' }}">{{ $order->user->name ?? 'Khách vãng lai' }}</div>
                                    <div class="text-[10px] text-slate-400 font-semibold truncate max-w-[150px]">{{ $order->user->email ?? '-' }}</div>
                                </td>
                                <!-- Receiver -->
                                <td class="p-4">
                                    <div class="font-medium text-slate-800 truncate max-w-[140px]" title="{{ $order->receiver_name }}">{{ $order->receiver_name }}</div>
                                    <div class="text-[10px] text-slate-400 font-semibold">{{ $order->receiver_phone }}</div>
                                </td>
                                <!-- Total -->
                                <td class="p-4 text-right font-extrabold text-slate-900 whitespace-nowrap">{{ number_format($order->total_amount, 0, ',', '.') }} ₫</td>
                                <!-- Payment Status -->
                                <td class="p-4 text-center whitespace-nowrap">
                                    <div class="inline-flex flex-col items-center gap-1">
                                        @if($order->payment_status === 'paid')
                                            <div class="relative group/tip inline-flex items-center justify-center">
                                                <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đã thanh toán">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                </span>
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                    <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đã thanh toán</span>
                                                    <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                                </div>
                                            </div>
                                        @elseif($order->payment_status === 'failed')
                                            <div class="relative group/tip inline-flex items-center justify-center">
                                                <span class="w-7 h-7 rounded-full bg-rose-100 text-rose-700 border border-rose-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Thanh toán thất bại">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                </span>
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                    <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Thất bại</span>
                                                    <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                                </div>
                                            </div>
                                        @elseif($order->payment_status === 'refunded')
                                            <div class="relative group/tip inline-flex items-center justify-center">
                                                <span class="w-7 h-7 rounded-full bg-purple-100 text-purple-700 border border-purple-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đã hoàn tiền">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                </span>
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                    <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đã hoàn tiền</span>
                                                    <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="relative group/tip inline-flex items-center justify-center">
                                                <span class="w-7 h-7 rounded-full bg-amber-100 text-amber-700 border border-amber-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Chưa thanh toán">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </span>
                                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                    <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Chưa thanh toán</span>
                                                    <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                                </div>
                                            </div>
                                        @endif
                                        <span class="text-[9px] font-mono px-1.5 py-0.2 rounded bg-slate-100 text-slate-500 font-bold border border-slate-200">
                                            {{ $order->payment_method === 'payos' ? 'PayOS' : ($order->payment_method === 'cod' ? 'COD' : ($order->payment_method === 'bank_transfer' ? 'VietQR' : strtoupper($order->payment_method))) }}
                                        </span>
                                    </div>
                                </td>
                                <!-- Order Status -->
                                <td class="p-4 text-center whitespace-nowrap">
                                    @if($order->order_status === 'completed')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Hoàn thành">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l5-5z" clip-rule="evenodd"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Hoàn thành</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @elseif($order->order_status === 'cancelled')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-rose-100 text-rose-700 border border-rose-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đã hủy">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đã hủy</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @elseif($order->order_status === 'pending')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-amber-100 text-amber-700 border border-amber-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help animate-pulse" title="Chờ xác nhận">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Chờ xác nhận</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @elseif($order->order_status === 'confirmed')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-sky-100 text-sky-700 border border-sky-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đã xác nhận">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đã xác nhận</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @elseif($order->order_status === 'processing')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 border border-indigo-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đang chuẩn bị hàng">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đang chuẩn bị</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @elseif($order->order_status === 'shipping')
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 border border-blue-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đang giao hàng">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đang giao hàng</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="relative group/tip inline-flex items-center justify-center">
                                            <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 border border-slate-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="{{ $order->order_status }}">
                                                <span class="text-[10px] font-bold uppercase">{{ substr($order->order_status, 0, 1) }}</span>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">{{ $order->order_status }}</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <!-- Action Link -->
                                <td class="p-4 text-center">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1 bg-slate-100 hover:bg-orange-50 text-slate-700 hover:text-orange-500 font-bold rounded-lg transition-all">
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-8 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        <p class="font-semibold text-slate-600">Không tìm thấy đơn hàng nào phù hợp.</p>
                                        <p class="text-xs text-slate-400">Thử tìm với từ khóa khác hoặc bấm Reset để xem tất cả đơn.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div>
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection
