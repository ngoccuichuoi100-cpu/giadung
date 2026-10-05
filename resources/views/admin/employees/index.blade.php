@extends('layouts.admin')

@section('title', 'Quản lý nhân sự - GiaDụngPro')

@section('content')
    <div class="mt-6 space-y-6 text-left">
        <!-- 3 Metric Cards for Staff -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Staff -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs flex items-center gap-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-slate-400 text-xs font-semibold block uppercase">Tổng Nhân Sự</span>
                    <span class="text-xl font-bold text-slate-800">{{ $totalStaff }}</span>
                </div>
            </div>

            <!-- Warehouse Staff -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs flex items-center gap-4">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-slate-400 text-xs font-semibold block uppercase">Nhân Viên Kho</span>
                    <span class="text-xl font-bold text-amber-600">{{ $warehouseCount }}</span>
                </div>
            </div>

            <!-- Sales Staff -->
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs flex items-center gap-4">
                <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-slate-400 text-xs font-semibold block uppercase">Nhân Viên Bán Hàng</span>
                    <span class="text-xl font-bold text-green-600">{{ $salesCount }}</span>
                </div>
            </div>
        </div>

        <!-- Main Panel Table Card -->
        <div id="admin-panel-wrapper" class="bg-white border border-slate-200 rounded-3xl shadow-xs text-left transition-all duration-300 relative">
            <!-- Sticky Header & Filter Bar (Chuyển đổi từ cố định sang chuyển động theo cuộn chuột) -->
            <div id="admin-sticky-bar" class="sticky top-0 z-20 bg-white border-b border-slate-100 p-6 rounded-t-3xl transition-all duration-300">
                <!-- Header Actions -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="font-bold text-slate-800 text-lg">Danh sách nhân viên</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Quản lý tài khoản nhân viên kho và nhân viên tư vấn bán hàng</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Scroll to top button (Tự động hiển thị khi navbar chuyển động) -->
                        <button type="button" id="sticky-scroll-top-btn" class="opacity-0 pointer-events-none scale-90 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center gap-1.5 shadow-2xs" title="Cuộn lên đầu trang">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            <span class="hidden md:inline">Lên đầu</span>
                        </button>

                        <a href="{{ route('admin.employees.create') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs whitespace-nowrap transition-all flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Thêm nhân viên
                        </a>
                    </div>
                </div>

                <!-- Filter bar -->
                <form action="{{ route('admin.employees.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100 mt-4">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tên, email, username..."
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

                    <!-- Filter Role -->
                    <div>
                        <select name="role" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <option value="">Tất cả vai trò</option>
                            <option value="warehouse" {{ request('role') === 'warehouse' ? 'selected' : '' }}>Nhân viên Kho</option>
                            <option value="sales" {{ request('role') === 'sales' ? 'selected' : '' }}>Nhân viên Bán hàng</option>
                        </select>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="flex-grow py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-all shadow-xs">
                            Lọc nhân viên
                        </button>
                        <a href="{{ route('admin.employees.index') }}" class="py-2 px-4 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-lg transition-all text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Employees Table & Pagination Container -->
            <div id="adminTableContainer" class="p-6 space-y-6">
                <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs text-slate-700">
                    <thead class="bg-slate-50/95 backdrop-blur-xs shadow-2xs">
                        <tr class="border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase">
                            <th class="p-4 whitespace-nowrap">Nhân viên</th>
                            <th class="p-4 whitespace-nowrap">Tên đăng nhập</th>
                            <th class="p-4 whitespace-nowrap">Số điện thoại</th>
                            <th class="p-4 text-center whitespace-nowrap">Vai trò</th>
                            <th class="p-4 text-center whitespace-nowrap w-24">Trạng thái</th>
                            <th class="p-4 whitespace-nowrap">Ngày tạo</th>
                            <th class="p-4 text-right whitespace-nowrap">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($employees as $emp)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="p-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold flex items-center justify-center border border-orange-200 uppercase">
                                            {{ substr($emp->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 block text-xs">{{ $emp->name }}</span>
                                            <span class="text-[11px] text-slate-400 block">{{ $emp->email ?: 'Chưa có email' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 font-mono font-medium text-slate-700 whitespace-nowrap">{{ $emp->username }}</td>
                                <td class="p-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-700 text-xs">{{ $emp->phone ?? '-' }}</div>
                                    @if($emp->residence_address)
                                        <div class="text-[10px] text-slate-400 truncate max-w-[180px] mt-0.5" title="Nơi cư trú: {{ $emp->residence_address }}">📍 {{ $emp->residence_address }}</div>
                                    @endif
                                </td>
                                <td class="p-4 text-center whitespace-nowrap">
                                    @if($emp->role === 'warehouse')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            Kho hàng
                                        </span>
                                    @elseif($emp->role === 'sales')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                            Bán hàng
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $emp->role }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-center whitespace-nowrap">
                                    @if($emp->status === 'active')
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
                                            <span class="w-7 h-7 rounded-full bg-rose-100 text-rose-700 border border-rose-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Tài khoản đã khóa">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                            </span>
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                                <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đã khóa</span>
                                                <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="p-4 text-slate-500 text-[11px] whitespace-nowrap">{{ $emp->created_at ? $emp->created_at->format('d/m/Y') : '-' }}</td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.employees.edit', $emp->id) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs transition-all">
                                            Sửa
                                        </a>
                                        <form action="{{ route('admin.employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản nhân viên này?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-lg text-xs transition-all">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400 italic">
                                    Không tìm thấy nhân viên nào phù hợp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

                <!-- Pagination -->
                @if($employees->hasPages())
                    <div class="pt-4 border-t border-slate-100">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection