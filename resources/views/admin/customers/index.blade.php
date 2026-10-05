@extends('layouts.admin')

@section('title', 'Quản lý khách hàng - GiaDụngPro')

@section('content')
    <div id="admin-panel-wrapper" class="mt-6 bg-white border border-slate-200 rounded-3xl shadow-xs text-left transition-all duration-300 relative">
        <!-- Sticky Header & Filter Bar (Chuyển đổi từ cố định sang chuyển động theo cuộn chuột) -->
        <div id="admin-sticky-bar" class="sticky top-0 z-20 bg-white border-b border-slate-100 p-6 rounded-t-3xl transition-all duration-300">
            <!-- Header Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-bold text-slate-800 text-lg">Quản lý khách hàng</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Danh sách người dùng đã đăng ký mua hàng trên hệ thống</p>
                </div>
                
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <!-- Search -->
                    <form action="{{ route('admin.customers.index') }}" method="GET" class="relative flex-grow sm:flex-grow-0">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tên, email, SĐT..."
                            autocomplete="off"
                            class="w-full sm:w-64 pl-9 pr-9 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">
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
                    </form>

                    <!-- Scroll to top button (Tự động hiển thị khi navbar chuyển động) -->
                    <button type="button" id="sticky-scroll-top-btn" class="opacity-0 pointer-events-none scale-90 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center gap-1.5 shadow-2xs" title="Cuộn lên đầu trang">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        <span class="hidden md:inline">Lên đầu</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Customers Table & Pagination Container -->
        <div id="adminTableContainer" class="p-6 space-y-6">
            <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs text-slate-700">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase">
                        <th class="p-4 w-12 text-center whitespace-nowrap">ID</th>
                        <th class="p-4 whitespace-nowrap">Khách hàng</th>
                        <th class="p-4 whitespace-nowrap">Số điện thoại</th>
                        <th class="p-4 whitespace-nowrap">Địa chỉ mặc định</th>
                        <th class="p-4 whitespace-nowrap">Ngày đăng ký</th>
                        <th class="p-4 text-center whitespace-nowrap w-24">Trạng thái</th>
                        <th class="p-4 text-center whitespace-nowrap w-36">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $cust)
                        <tr class="hover:bg-slate-50/50">
                            <!-- ID -->
                            <td class="p-4 text-center font-mono font-bold text-slate-500 whitespace-nowrap">
                                #{{ $cust->id }}
                            </td>
                            <!-- Basic details -->
                            <td class="p-4">
                                <div class="font-bold text-slate-900 truncate max-w-[150px]" title="{{ $cust->name }}">{{ $cust->name }}</div>
                                <div class="text-[10px] text-slate-400 font-semibold truncate max-w-[150px]">{{ $cust->email }}</div>
                            </td>
                            <!-- Phone -->
                            <td class="p-4 font-semibold text-slate-600 whitespace-nowrap">{{ $cust->phone ?? '-' }}</td>
                            <!-- Address -->
                            <td class="p-4 text-slate-400 max-w-xs truncate" title="{{ $cust->address }}">
                                {{ $cust->address ?? 'Chưa cập nhật' }}
                            </td>
                            <!-- Date -->
                            <td class="p-4 text-slate-500 whitespace-nowrap">{{ $cust->created_at->format('d/m/Y') }}</td>
                            <!-- Status -->
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($cust->status === 'active')
                                    <div class="relative group/tip inline-flex items-center justify-center">
                                        <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Tài khoản hoạt động">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        </span>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                            <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Hoạt động</span>
                                            <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                        </div>
                                    </div>
                                @else
                                    <div class="relative group/tip inline-flex items-center justify-center">
                                        <span class="w-7 h-7 rounded-full bg-rose-100 text-rose-700 border border-rose-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Tài khoản bị khóa">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        </span>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                            <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đã khóa</span>
                                            <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <!-- Actions -->
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.customers.show', $cust->id) }}" class="text-[10px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 py-1 px-2.5 rounded transition-all">
                                        Chi tiết
                                    </a>

                                    <form action="{{ route('admin.customers.toggle-status', $cust->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái hoạt động của tài khoản này?');">
                                        @csrf
                                        @method('PATCH')
                                        @if($cust->status === 'active')
                                            <button type="submit" class="text-[10px] font-bold bg-amber-50 hover:bg-amber-100 text-amber-700 py-1 px-2 rounded transition-all">
                                                Khóa
                                            </button>
                                        @else
                                            <button type="submit" class="text-[10px] font-bold bg-green-50 hover:bg-green-100 text-green-700 py-1 px-2 rounded transition-all">
                                                Mở
                                            </button>
                                        @endif
                                    </form>

                                    <!-- Xóa tài khoản -->
                                    <form action="{{ route('admin.customers.destroy', $cust->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn XÓA VĨNH VIỄN khách hàng #{{ $cust->id }} ({{ $cust->name ?? $cust->username }})?\n\nHệ thống sẽ xóa tài khoản và tự động sắp xếp lại thứ tự ID trong CSDL.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[10px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 py-1 px-2 rounded transition-all flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400 italic">Không tìm thấy khách hàng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div>
            {{ $customers->links() }}
        </div>
        </div>
    </div>
@endsection
