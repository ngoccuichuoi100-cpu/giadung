@extends('layouts.admin')

@section('title', 'Sửa danh mục ' . $category->name . ' - GiaDụngPro')

@section('content')
    <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-xs max-w-2xl mx-auto space-y-6">
        <div>
            <a href="{{ route('admin.categories.index') }}" class="text-xs font-semibold text-slate-500 hover:text-orange-500 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại danh sách danh mục
            </a>
            <h2 class="font-bold text-slate-800 text-lg">Sửa danh mục: {{ $category->name }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">Cập nhật thông tin chi tiết cho danh mục</p>
        </div>

        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tên danh mục</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" required
                    class="w-full px-4 py-2 bg-slate-50 border @error('name') border-red-500 @else border-slate-200 @enderror rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">
                @error('name')
                    <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Slug (Đường dẫn thân thiện)</label>
                <input type="text" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" required
                    class="w-full px-4 py-2 bg-slate-50 border @error('slug') border-red-500 @else border-slate-200 @enderror rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">
                @error('slug')
                    <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mô tả chi tiết</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">{{ old('description', $category->description) }}</textarea>
            </div>

            <!-- Image File & Current Preview -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Hình ảnh hiện tại</label>
                <div class="flex items-center gap-4 mb-3">
                    <div class="w-16 h-16 bg-slate-50 border border-slate-200 rounded-xl overflow-hidden flex-shrink-0">
                        @if($category->image)
                            <img src="{{ $category->image_url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-350">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                    </div>
                    <span class="text-xs text-slate-400">Chọn ảnh mới bên dưới nếu muốn thay đổi</span>
                </div>
                <input type="file" id="image" name="image" accept="image/*"
                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                @error('image')
                    <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status selector -->
            <div>
                <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Trạng thái hoạt động</label>
                <select id="status" name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white">
                    <option value="active" {{ old('status', $category->status) === 'active' ? 'selected' : '' }}>Hiển thị (Hoạt động)</option>
                    <option value="inactive" {{ old('status', $category->status) === 'inactive' ? 'selected' : '' }}>Ẩn (Ngừng hoạt động)</option>
                </select>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition-all">
                    Cập nhật danh mục
                </button>
            </div>
        </form>
    </div>

    <!-- Slug automatic generation script -->
    <script>
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');

        if(nameInput && slugInput) {
            nameInput.addEventListener('input', function() {
                let slug = nameInput.value.toLowerCase();
                // Replace Vietnamese tones
                slug = slug.replace(/á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/g, 'a');
                slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/g, 'e');
                slug = slug.replace(/í|ì|ỉ|ĩ|ị/g, 'i');
                slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/g, 'o');
                slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/g, 'u');
                slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/g, 'y');
                slug = slug.replace(/đ/g, 'd');
                // Replace special characters
                slug = slug.replace(/[^a-z0-9\s-]/g, '');
                // Replace space with -
                slug = slug.trim().replace(/\s+/g, '-');
                slugInput.value = slug;
            });
        }
    </script>
@endsection
