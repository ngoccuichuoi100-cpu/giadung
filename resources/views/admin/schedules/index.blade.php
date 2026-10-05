@extends('layouts.admin')

@section('title', 'Lịch trực bán hàng - GiaDụngPro')

@section('content')
    <div id="admin-panel-wrapper" class="mt-6 bg-white border border-slate-200 rounded-3xl shadow-xs text-left transition-all duration-300 relative">
        <!-- Sticky Header & Filter Bar (Chuyển đổi từ cố định sang chuyển động theo cuộn chuột) -->
        <div id="admin-sticky-bar" class="sticky top-0 z-20 bg-white border-b border-slate-100 p-6 rounded-t-3xl transition-all duration-300">
            <!-- Header Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-bold text-slate-800 text-lg">Lịch trực bán hàng</h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        @if(auth()->user()->isAdmin())
                            Phân công ngày trực, ca làm việc và nhiệm vụ cho nhân viên tư vấn bán hàng
                        @else
                            Danh sách ca trực bán hàng được phân công cho bạn
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2 self-end sm:self-auto">
                    <!-- Scroll to top button (Tự động hiển thị khi navbar chuyển động) -->
                    <button type="button" id="sticky-scroll-top-btn" class="opacity-0 pointer-events-none scale-90 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center gap-1.5 shadow-2xs" title="Cuộn lên đầu trang">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        <span class="hidden md:inline">Lên đầu</span>
                    </button>

                    @if(auth()->user()->isAdmin())
                        <button onclick="document.getElementById('assignModal').classList.remove('hidden')" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs whitespace-nowrap transition-all flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Phân công ca trực mới
                        </button>
                    @endif
                </div>
            </div>

            <!-- Filter bar -->
            <form action="{{ route('admin.sales-schedules.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100 mt-4">
                <!-- Search -->
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm nhân viên, ghi chú..."
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
                @if(auth()->user()->isAdmin())
                    <div>
                        <select name="user_id" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <option value="">Tất cả nhân viên bán hàng</option>
                            @foreach($salesStaff as $staff)
                                <option value="{{ $staff->id }}" {{ request('user_id') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }} ({{ $staff->username }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div>
                        <input type="text" value="Cá nhân: {{ auth()->user()->name }}" disabled class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-xs text-slate-500">
                    </div>
                @endif

                <div>
                    <input type="date" name="date" value="{{ request('date') }}" placeholder="Chọn ngày..."
                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                </div>

                <div>
                    <input type="month" name="month" value="{{ request('month') }}"
                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-grow py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-all shadow-xs">
                        Lọc ca trực
                    </button>
                    <a href="{{ route('admin.sales-schedules.index') }}" class="py-2 px-4 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-lg transition-all text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Schedules Table & Pagination Container -->
        <div id="adminTableContainer" class="p-6 space-y-6">
            <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs text-slate-700">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-550 uppercase">
                        <th class="p-4">Ngày trực</th>
                        <th class="p-4">Nhân viên bán hàng</th>
                        <th class="p-4">Ca làm việc</th>
                        <th class="p-4">Nhiệm vụ / Ghi chú</th>
                        <th class="p-4 text-center">Trạng thái</th>
                        <th class="p-4 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($schedules as $sched)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-4">
                                <span class="font-bold text-slate-900 block text-xs">
                                    {{ $sched->work_date->format('d/m/Y') }}
                                </span>
                                <span class="text-[10px] font-semibold {{ $sched->work_date->isToday() ? 'text-orange-500' : 'text-slate-400' }}">
                                    {{ $sched->work_date->isToday() ? '★ Hôm nay' : ($sched->work_date->isFuture() ? 'Sắp tới' : 'Đã qua') }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center border border-slate-200 text-[11px] uppercase">
                                        {{ substr($sched->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block text-xs">{{ $sched->user->name }}</span>
                                        <span class="text-[11px] text-slate-400 block">{{ $sched->user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                    {{ $sched->shift_name }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-600 max-w-[220px]">
                                <span class="line-clamp-2">{{ $sched->notes ?: 'Không có ghi chú.' }}</span>
                            </td>
                            <td class="p-4 text-center">
                                @if($sched->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">
                                        Đã hoàn thành
                                    </span>
                                @elseif($sched->status === 'cancelled')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                        Đã hủy
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        Đã phân công
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(auth()->user()->isAdmin())
                                        <button onclick="document.getElementById('editModal{{ $sched->id }}').classList.remove('hidden')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs transition-all">
                                            Sửa
                                        </button>
                                        <form action="{{ route('admin.sales-schedules.destroy', $sched->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa ca làm việc này?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-600 font-bold rounded-lg text-xs transition-all">
                                                Xóa
                                            </button>
                                        </form>
                                    @elseif(auth()->id() === $sched->user_id)
                                        <button onclick="document.getElementById('editModal{{ $sched->id }}').classList.remove('hidden')" class="px-2.5 py-1 bg-orange-50 hover:bg-orange-100 text-orange-600 font-bold rounded-lg text-xs transition-all flex items-center gap-1">
                                            Báo cáo / Cập nhật
                                        </button>
                                    @else
                                        <span class="text-slate-400 text-[11px] italic">Chỉ xem</span>
                                    @endif
                                </div>

                                <!-- Edit Modal for this schedule -->
                                <div id="editModal{{ $sched->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl text-left border border-slate-200 space-y-4">
                                        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                                            <h3 class="font-bold text-slate-900 text-sm">
                                                Cập nhật ca trực: {{ $sched->user->name }} ({{ $sched->work_date->format('d/m/Y') }})
                                            </h3>
                                            <button type="button" onclick="document.getElementById('editModal{{ $sched->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg leading-none font-bold">&times;</button>
                                        </div>

                                        <form action="{{ route('admin.sales-schedules.update', $sched->id) }}" method="POST" class="space-y-4">
                                            @csrf
                                            @method('PUT')

                                            @if(auth()->user()->isAdmin())
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Ca làm việc</label>
                                                    <select name="shift_name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                                                        <option value="Ca Sáng (08:00 - 12:00)" {{ $sched->shift_name === 'Ca Sáng (08:00 - 12:00)' ? 'selected' : '' }}>Ca Sáng (08:00 - 12:00)</option>
                                                        <option value="Ca Chiều (13:00 - 17:00)" {{ $sched->shift_name === 'Ca Chiều (13:00 - 17:00)' ? 'selected' : '' }}>Ca Chiều (13:00 - 17:00)</option>
                                                        <option value="Ca Tối (17:00 - 21:00)" {{ $sched->shift_name === 'Ca Tối (17:00 - 21:00)' ? 'selected' : '' }}>Ca Tối (17:00 - 21:00)</option>
                                                        <option value="Cả Ngày (08:00 - 17:00)" {{ $sched->shift_name === 'Cả Ngày (08:00 - 17:00)' ? 'selected' : '' }}>Cả Ngày (08:00 - 17:00)</option>
                                                    </select>
                                                </div>
                                            @else
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 mb-1">Ca làm việc</label>
                                                    <input type="text" value="{{ $sched->shift_name }}" disabled class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-500">
                                                </div>
                                            @endif

                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">Trạng thái ca trực <span class="text-red-500">*</span></label>
                                                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 font-bold">
                                                    <option value="assigned" {{ $sched->status === 'assigned' ? 'selected' : '' }}>Đã phân công (Chờ trực)</option>
                                                    <option value="completed" {{ $sched->status === 'completed' ? 'selected' : '' }}>Đã hoàn thành ca trực</option>
                                                    @if(auth()->user()->isAdmin())
                                                        <option value="cancelled" {{ $sched->status === 'cancelled' ? 'selected' : '' }}>Hủy ca trực</option>
                                                    @endif
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 mb-1">Ghi chú / Báo cáo ca trực</label>
                                                <textarea name="notes" rows="3" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500" placeholder="Nhập ghi chú hoặc kết quả bán hàng trong ca...">{{ $sched->notes }}</textarea>
                                            </div>

                                            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                                                <button type="button" onclick="document.getElementById('editModal{{ $sched->id }}').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all">
                                                    Đóng
                                                </button>
                                                <button type="submit" class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                                                    Lưu cập nhật
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 italic">
                                Chưa có phân công lịch trực bán hàng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($schedules->hasPages())
            <div class="pt-4 border-t border-slate-100">
                {{ $schedules->links() }}
            </div>
        @endif
        </div>
    </div>

    <!-- Create Schedule Modal for Admin -->
    @if(auth()->user()->isAdmin())
        <div id="assignModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 text-left">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm">Phân công ngày trực bán hàng mới</h3>
                    <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg leading-none font-bold">&times;</button>
                </div>

                <form action="{{ route('admin.sales-schedules.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nhân viên bán hàng <span class="text-red-500">*</span></label>
                        <select name="user_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                            <option value="">-- Chọn nhân viên bán hàng --</option>
                            @foreach($salesStaff as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->username }} - {{ $staff->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ngày trực <span class="text-red-500">*</span></label>
                            <input type="date" name="work_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ca làm việc <span class="text-red-500">*</span></label>
                            <select name="shift_name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500">
                                <option value="Ca Sáng (08:00 - 12:00)">Ca Sáng (08:00 - 12:00)</option>
                                <option value="Ca Chiều (13:00 - 17:00)">Ca Chiều (13:00 - 17:00)</option>
                                <option value="Ca Tối (17:00 - 21:00)">Ca Tối (17:00 - 21:00)</option>
                                <option value="Cả Ngày (08:00 - 17:00)">Cả Ngày (08:00 - 17:00)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Ghi chú / Nhiệm vụ bàn giao</label>
                        <textarea name="notes" rows="3" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500" placeholder="Ví dụ: Trực quầy Nồi cơm điện, giải đáp thắc mắc khách hàng..."></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all">
                            Hủy
                        </button>
                        <button type="submit" class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                            Phân công ca trực
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection