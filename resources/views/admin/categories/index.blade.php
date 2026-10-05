@extends('layouts.admin')

@section('title', 'Quản lý danh mục - GiaDụngPro')

@section('content')
    <div id="admin-panel-wrapper" class="mt-6 bg-white border border-slate-200 rounded-3xl shadow-xs text-left transition-all duration-300 relative">
        <!-- Sticky Header & Filter Bar (Chuyển đổi từ cố định sang chuyển động theo cuộn chuột) -->
        <div id="admin-sticky-bar" class="sticky top-0 z-20 bg-white border-b border-slate-100 p-6 rounded-t-3xl transition-all duration-300">
            <!-- Header Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-bold text-slate-800 text-lg">Danh sách danh mục</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Quản lý các loại sản phẩm gia dụng đang kinh doanh</p>
                </div>
                
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <!-- Search -->
                    <form action="{{ route('admin.categories.index') }}" method="GET" class="relative flex-grow sm:flex-grow-0">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm tên danh mục..."
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

                    <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs whitespace-nowrap transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Thêm danh mục
                    </a>
                </div>
            </div>
        </div>

        <!-- Categories Table & Pagination Container -->
        <div id="adminTableContainer" class="p-6 space-y-6">
            <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs text-slate-700">
                <thead class="bg-slate-50/95 backdrop-blur-xs shadow-2xs">
                    <tr class="border-b border-slate-200 text-[10px] font-bold text-slate-500 uppercase">
                        <th class="p-4 whitespace-nowrap">Hình ảnh</th>
                        <th class="p-4 whitespace-nowrap">Tên danh mục</th>
                        <th class="p-4 whitespace-nowrap">Slug</th>
                        <th class="p-4 whitespace-nowrap">Mô tả</th>
                        <th class="p-4 text-center whitespace-nowrap w-24">Trạng thái</th>
                        <th class="p-4 text-center whitespace-nowrap w-24">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/50">
                            <!-- Image -->
                            <td class="p-4 whitespace-nowrap">
                                <div class="w-10 h-10 bg-slate-50 border border-slate-100 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($category->image)
                                        <img src="{{ $category->image_url }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-350">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <!-- Name -->
                            <td class="p-4 font-bold text-slate-900 whitespace-nowrap">{{ $category->name }}</td>
                            <!-- Slug -->
                            <td class="p-4 text-slate-500 font-mono whitespace-nowrap">{{ $category->slug }}</td>
                            <!-- Description -->
                            <td class="p-4 text-slate-400 max-w-xs truncate" title="{{ $category->description }}">
                                {{ $category->description ?? 'Chưa có mô tả' }}
                            </td>
                            <!-- Status -->
                            <td class="p-4 text-center whitespace-nowrap">
                                @if($category->status === 'active')
                                    <div class="relative group/tip inline-flex items-center justify-center">
                                        <span class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đang hiển thị">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </span>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                            <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đang hiển thị</span>
                                            <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                        </div>
                                    </div>
                                @else
                                    <div class="relative group/tip inline-flex items-center justify-center">
                                        <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 border border-slate-200/90 flex items-center justify-center shadow-2xs hover:scale-110 transition-transform cursor-help" title="Đang ẩn">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                                        </span>
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 hidden group-hover/tip:flex flex-col items-center pointer-events-none z-30">
                                            <span class="px-2 py-0.5 text-[10px] font-bold text-white whitespace-nowrap bg-slate-900 rounded-md shadow-md">Đang ẩn</span>
                                            <div class="w-1.5 h-1.5 -mt-0.5 rotate-45 bg-slate-900"></div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <!-- Actions -->
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-2.5">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="p-1 bg-slate-100 hover:bg-orange-50 text-slate-600 hover:text-orange-500 rounded transition-colors" title="Sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này? Không thể xóa nếu có sản phẩm liên kết.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-500 rounded transition-colors" title="Xóa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400 italic">Không tìm thấy danh mục nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div>
            {{ $categories->links() }}
        </div>
        </div>
    </div>
@endsection
