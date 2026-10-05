<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!auth()->check() || !auth()->user()->isAdmin()) {
                    abort(403, 'Chỉ Quản trị viên mới có quyền truy cập khu vực quản lý nhân sự.');
                }
                return $next($request);
            }),
        ];
    }

    public function index(Request $request)
    {
        $query = User::whereIn('role', ['warehouse', 'sales']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            if (mb_strlen($q, 'UTF-8') === 1) {
                // Gõ 1 ký tự: Chỉ tìm các từ bắt đầu bằng ký tự này
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "{$q}%")
                        ->orWhere('name', 'like', "% {$q}%")
                        ->orWhere('email', 'like', "{$q}%")
                        ->orWhere('phone', 'like', "{$q}%")
                        ->orWhere('username', 'like', "{$q}%");
                });
            } else {
                // Gõ >= 2 ký tự: Tìm linh hoạt ở mọi vị trí
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('username', 'like', "%{$q}%")
                        ->orWhere('residence_address', 'like', "%{$q}%")
                        ->orWhere('workplace', 'like', "%{$q}%");
                });
            }
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $totalStaff = User::whereIn('role', ['warehouse', 'sales'])->count();
        $warehouseCount = User::where('role', 'warehouse')->count();
        $salesCount = User::where('role', 'sales')->count();

        return view('admin.employees.index', compact('employees', 'totalStaff', 'warehouseCount', 'salesCount'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:3',
            'role' => 'required|in:warehouse,sales',
            'residence_address' => 'nullable|string|max:255',
            'workplace' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ], [
            'username.unique' => 'Tên đăng nhập này đã được sử dụng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.required' => 'Vui lòng nhập số điện thoại của nhân sự.',
            'role.in' => 'Vai trò nhân viên không hợp lệ.',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->filled('email') ? $request->email : null,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'residence_address' => $request->residence_address,
            'workplace' => $request->workplace ?? $request->address ?? 'Hệ thống GiaDụngPro',
            'address' => $request->residence_address ?? $request->address ?? 'Hệ thống GiaDụngPro',
            'role' => $request->role,
            'status' => 'active',
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Thêm tài khoản nhân viên thành công.');
    }

    public function edit($id)
    {
        $employee = User::whereIn('role', ['warehouse', 'sales'])->findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = User::whereIn('role', ['warehouse', 'sales'])->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $employee->id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:3',
            'role' => 'required|in:warehouse,sales',
            'status' => 'required|in:active,locked',
            'residence_address' => 'nullable|string|max:255',
            'workplace' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ], [
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.required' => 'Vui lòng nhập số điện thoại của nhân sự.',
            'role.in' => 'Vai trò nhân viên không hợp lệ.',
        ]);

        $employee->name = $request->name;
        $employee->email = $request->filled('email') ? $request->email : null;
        $employee->phone = $request->phone;
        $employee->role = $request->role;
        $employee->status = $request->status;
        $employee->residence_address = $request->residence_address;
        $employee->workplace = $request->workplace ?? $request->address;
        $employee->address = $request->residence_address ?? $request->address;

        if ($request->filled('password')) {
            $employee->password = Hash::make($request->password);
        }

        $employee->save();

        return redirect()->route('admin.employees.index')->with('success', 'Cập nhật thông tin nhân viên thành công.');
    }

    public function destroy($id)
    {
        $employee = User::whereIn('role', ['warehouse', 'sales'])->findOrFail($id);
        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Xóa tài khoản nhân viên thành công.');
    }
}