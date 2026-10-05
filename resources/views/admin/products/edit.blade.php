@extends('layouts.admin')

@section('title', 'Sửa sản phẩm ' . $product->name . ' - GiaDụngPro')

@section('content')
    <div class="mt-6 bg-white border border-slate-200 rounded-2xl p-8 shadow-xs max-w-4xl mx-auto space-y-6">
        <div>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-orange-500 flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại danh sách sản phẩm
            </a>
            <h2 class="font-bold text-slate-800 text-lg">Sửa sản phẩm: {{ $product->name }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">Cập nhật thông tin chi tiết, giá cả và hình ảnh sản phẩm</p>
        </div>

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Category Select -->
                <div>
                    <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Danh mục đồ gia dụng</label>
                    <select id="category_id" name="category_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Code -->
                <div>
                    <label for="code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mã sản phẩm (duy nhất)</label>
                    <input type="text" id="code" name="code" value="{{ old('code', $product->code) }}" required
                        class="w-full px-4 py-2 bg-slate-50 border @error('code') border-red-500 @else border-slate-200 @enderror rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">
                    @error('code')
                        <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Name -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tên sản phẩm</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                        class="w-full px-4 py-2 bg-slate-50 border @error('name') border-red-500 @else border-slate-200 @enderror rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">
                    @error('name')
                        <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Slug -->
                <div>
                    <label for="slug" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Slug (Đường dẫn thân thiện)</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $product->slug) }}" required
                        class="w-full px-4 py-2 bg-slate-50 border @error('slug') border-red-500 @else border-slate-200 @enderror rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">
                    @error('slug')
                        <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Brand -->
                <div>
                    <label for="brand" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Thương hiệu</label>
                    <input type="text" id="brand" name="brand" value="{{ old('brand', $product->brand) }}"
                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">
                </div>

                <!-- Unit -->
                <div>
                    <label for="unit" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Đơn vị tính</label>
                    <input type="text" id="unit" name="unit" value="{{ old('unit', $product->unit) }}" required
                        class="w-full px-4 py-2 bg-slate-50 border @error('unit') border-red-500 @else border-slate-200 @enderror rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">
                    @error('unit')
                        <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Short Description -->
            <div>
                <label for="short_description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mô tả ngắn</label>
                <textarea id="short_description" name="short_description" rows="2"
                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">{{ old('short_description', $product->short_description) }}</textarea>
            </div>

            <!-- Detailed Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mô tả chi tiết sản phẩm</label>
                <textarea id="description" name="description" rows="5"
                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Original Price -->
                <div>
                    <label for="price" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Giá gốc (VND)</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" required min="0" step="any"
                        class="w-full px-4 py-2 bg-slate-50 border @error('price') border-red-500 @else border-slate-200 @enderror rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">
                    @error('price')
                        <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sale Price -->
                <div>
                    <label for="sale_price" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Giá khuyến mãi (VND)</label>
                    <input type="number" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0" step="any"
                        class="w-full px-4 py-2 bg-slate-50 border @error('sale_price') border-red-500 @else border-slate-200 @enderror rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">
                    @error('sale_price')
                        <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stock Quantity -->
                <div>
                    <label for="stock_quantity" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Số lượng tồn kho</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required min="0"
                        class="w-full px-4 py-2 bg-slate-50 border @error('stock_quantity') border-red-500 @else border-slate-200 @enderror rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white transition-all">
                    @error('stock_quantity')
                        <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Current Images & New Upload inputs -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-slate-100 pt-6">
                <!-- Thumbnail -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ảnh đại diện hiện tại</label>
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-16 h-16 bg-slate-50 border border-slate-200 rounded-xl overflow-hidden flex-shrink-0">
                            @if($product->thumbnail)
                                <img src="{{ $product->thumbnail_url }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Thay đổi ảnh đại diện mới</span>
                    </div>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                    @error('thumbnail')
                        <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gallery Upload -->
                <div>
                    <label for="gallery" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Bổ sung hình ảnh chi tiết mới</label>
                    <input type="file" id="gallery" name="gallery[]" accept="image/*" multiple
                        class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                    @error('gallery.*')
                        <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Existing Gallery Manage widget -->
            @if($gallery->isNotEmpty())
                <div class="border-t border-slate-100 pt-6">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Quản lý thư viện ảnh chi tiết</label>
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-4">
                        @foreach($gallery as $img)
                            <div class="aspect-square bg-slate-50 border border-slate-200 rounded-xl overflow-hidden relative group">
                                <img src="{{ $img->image_url }}" class="w-full h-full object-cover">
                                <!-- Delete Overlay button -->
                                <button type="button" onclick="confirmDeleteImage({{ $img->id }})" class="absolute inset-0 bg-red-900/60 backdrop-blur-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2 border-t border-slate-100">
                <!-- Status -->
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Trạng thái bán hàng</label>
                    <select id="status" name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white">
                        <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Hiển thị (Đang bán)</option>
                        <option value="inactive" {{ old('status', $product->status) === 'inactive' ? 'selected' : '' }}>Ẩn (Tạm ngưng bán)</option>
                    </select>
                </div>

                <!-- Featured -->
                <div class="flex items-center pt-6">
                    <label class="flex items-center text-xs font-bold text-slate-700 uppercase tracking-wider cursor-pointer select-none">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                            class="w-4 h-4 rounded text-orange-500 focus:ring-orange-500 border-slate-300 mr-2.5">
                        Đánh dấu sản phẩm nổi bật
                    </label>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-xs transition-all cursor-pointer">
                    Lưu cập nhật sản phẩm
                </button>
            </div>
        </form>
    </div>

    <!-- Hidden form for deleting single gallery image -->
    <form id="delete-image-form" action="" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

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

        function confirmDeleteImage(id) {
            if (confirm('Bạn có chắc chắn muốn xóa ảnh chi tiết này khỏi sản phẩm?')) {
                const form = document.getElementById('delete-image-form');
                form.action = '/admin/products/images/' + id;
                form.submit();
            }
        }
    </script>
@endsection
