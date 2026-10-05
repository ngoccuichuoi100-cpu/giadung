<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->isWarehouse())) {
                    abort(403, 'Chỉ Quản trị viên hoặc Nhân viên kho mới có quyền thêm/sửa/xóa danh mục.');
                }
                return $next($request);
            }, only: ['create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('q')) {
            $q = trim($request->q);
            if (mb_strlen($q, 'UTF-8') === 1) {
                // Gõ 1 ký tự: Chỉ tìm các từ bắt đầu bằng ký tự này (đầu câu hoặc sau dấu cách)
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "{$q}%")
                        ->orWhere('name', 'like', "% {$q}%");
                });
            } else {
                // Gõ >= 2 ký tự: Tìm linh hoạt ở mọi vị trí
                $query->where('name', 'like', "%{$q}%");
            }
        }

        $categories = $query->latest()->paginate(20)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'slug.required' => 'Slug là bắt buộc.',
            'slug.unique' => 'Slug đã tồn tại.',
            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.max' => 'Dung lượng ảnh tối đa là 2MB.',
        ]);

        $data = $request->only(['name', 'slug', 'description', 'status']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = $path;
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Tạo danh mục thành công.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:active,inactive',
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'slug.required' => 'Slug là bắt buộc.',
            'slug.unique' => 'Slug đã tồn tại.',
            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.max' => 'Dung lượng ảnh tối đa là 2MB.',
        ]);

        $data = $request->only(['name', 'slug', 'description', 'status']);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = $path;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Không thể xóa danh mục này vì đang có sản phẩm thuộc danh mục. Vui lòng chuyển hoặc xóa các sản phẩm liên quan trước.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công.');
    }
}
