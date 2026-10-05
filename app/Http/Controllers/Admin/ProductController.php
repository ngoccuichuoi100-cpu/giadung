<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->isWarehouse())) {
                    abort(403, 'Chỉ Quản trị viên hoặc Nhân viên kho mới có quyền thêm/sửa/xóa sản phẩm.');
                }
                return $next($request);
            }, only: ['create', 'store', 'show', 'edit', 'update', 'destroy', 'deleteGalleryImage']),
        ];
    }

    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('q')) {
            $q = trim($request->q);
            if (mb_strlen($q, 'UTF-8') === 1) {
                // Gõ 1 ký tự: Chỉ tìm các từ bắt đầu bằng ký tự này hoặc mã sản phẩm
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "{$q}%")
                        ->orWhere('name', 'like', "% {$q}%")
                        ->orWhere('code', 'like', "{$q}%");
                });
            } else {
                // Gõ >= 2 ký tự: Tìm linh hoạt ở mọi vị trí
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%");
                });
            }
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(20)->withQueryString();
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        $brands = \App\Models\Brand::where('status', 'active')->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'code' => 'required|string|max:100|unique:products,code',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'brand' => 'nullable|string|max:100',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
            'is_domestic' => 'boolean',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'code.required' => 'Mã sản phẩm là bắt buộc.',
            'code.unique' => 'Mã sản phẩm đã tồn tại.',
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'slug.required' => 'Slug là bắt buộc.',
            'slug.unique' => 'Slug đã tồn tại.',
            'price.required' => 'Giá gốc là bắt buộc.',
            'price.numeric' => 'Giá gốc phải là số.',
            'price.min' => 'Giá gốc không được âm.',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số.',
            'sale_price.min' => 'Giá khuyến mãi không được âm.',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            'stock_quantity.required' => 'Số lượng tồn kho là bắt buộc.',
            'stock_quantity.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'stock_quantity.min' => 'Số lượng tồn kho không được âm.',
            'thumbnail.required' => 'Ảnh đại diện là bắt buộc.',
            'thumbnail.image' => 'File ảnh đại diện không hợp lệ.',
            'thumbnail.max' => 'Dung lượng ảnh đại diện tối đa là 2MB.',
            'gallery.*.image' => 'File ảnh chi tiết không hợp lệ.',
            'gallery.*.max' => 'Dung lượng mỗi ảnh chi tiết tối đa là 2MB.',
        ]);

        $data = $request->only([
            'category_id', 'brand_id', 'code', 'name', 'slug', 'brand',
            'short_description', 'description', 'price',
            'sale_price', 'stock_quantity', 'unit', 'status'
        ]);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_domestic'] = $request->boolean('is_domestic');

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('products/thumbnails', 'public');
            $data['thumbnail'] = $path;
        }

        $product = Product::create($data);

        // Handle gallery images upload
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $index => $imageFile) {
                $path = $imageFile->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Tạo sản phẩm thành công.');
    }

    public function show(Product $product)
    {
        return redirect()->route('admin.products.edit', $product->id);
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = \App\Models\Brand::where('status', 'active')->get();
        $gallery = $product->images()->orderBy('sort_order')->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands', 'gallery'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'code' => 'required|string|max:100|unique:products,code,' . $product->id,
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'brand' => 'nullable|string|max:100',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
            'is_domestic' => 'boolean',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'code.required' => 'Mã sản phẩm là bắt buộc.',
            'code.unique' => 'Mã sản phẩm đã tồn tại.',
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'slug.required' => 'Slug là bắt buộc.',
            'slug.unique' => 'Slug đã tồn tại.',
            'price.required' => 'Giá gốc là bắt buộc.',
            'price.numeric' => 'Giá gốc phải là số.',
            'price.min' => 'Giá gốc không được âm.',
            'sale_price.numeric' => 'Giá khuyến mãi phải là số.',
            'sale_price.min' => 'Giá khuyến mãi không được âm.',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            'stock_quantity.required' => 'Số lượng tồn kho là bắt buộc.',
            'stock_quantity.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'stock_quantity.min' => 'Số lượng tồn kho không được âm.',
            'thumbnail.image' => 'File ảnh đại diện không hợp lệ.',
            'thumbnail.max' => 'Dung lượng ảnh đại diện tối đa là 2MB.',
            'gallery.*.image' => 'File ảnh chi tiết không hợp lệ.',
            'gallery.*.max' => 'Dung lượng mỗi ảnh chi tiết tối đa là 2MB.',
        ]);

        $data = $request->only([
            'category_id', 'brand_id', 'code', 'name', 'slug', 'brand',
            'short_description', 'description', 'price',
            'sale_price', 'stock_quantity', 'unit', 'status'
        ]);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_domestic'] = $request->boolean('is_domestic');

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            $path = $request->file('thumbnail')->store('products/thumbnails', 'public');
            $data['thumbnail'] = $path;
        }

        $product->update($data);

        // Handle new gallery images upload
        if ($request->hasFile('gallery')) {
            $lastImage = ProductImage::where('product_id', $product->id)->orderBy('sort_order', 'desc')->first();
            $startOrder = $lastImage ? $lastImage->sort_order + 1 : 0;

            foreach ($request->file('gallery') as $index => $imageFile) {
                $path = $imageFile->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => $startOrder + $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Product $product)
    {
        // Delete gallery images files and records
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image_path);
            $img->delete();
        }

        // Delete thumbnail file
        if ($product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công.');
    }

    public function deleteGalleryImage($id)
    {
        $image = ProductImage::findOrFail($id);

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Đã xóa ảnh chi tiết sản phẩm.');
    }
}
