<?php

namespace App\Http\Controllers;

use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function index()
    {
        $user = Auth::user();
        $provinces = $this->locationService->getProvinces();
        return view('profile', compact('user', 'provinces'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:20'],
            'province_code' => ['nullable', 'string', 'exists:provinces,code'],
            'district_code' => ['nullable', 'string', 'exists:districts,code'],
            'ward_code' => ['nullable', 'string', 'exists:wards,code'],
            'detail_address' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Họ tên là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.required' => 'Số điện thoại là bắt buộc.',
        ]);

        $finalAddress = $request->address;

        // If cascading location is selected, assemble the full address
        if ($request->province_code && $request->district_code && $request->ward_code) {
            $loc = $this->locationService->getLocationNames($request->province_code, $request->district_code, $request->ward_code);
            $parts = array_filter([
                $request->detail_address,
                $loc['ward'],
                $loc['district'],
                $loc['province']
            ]);
            $finalAddress = implode(', ', $parts);
        } elseif ($request->detail_address) {
            $finalAddress = $request->detail_address;
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $finalAddress,
            'province_code' => $request->province_code,
            'district_code' => $request->district_code,
            'ward_code' => $request->ward_code,
            'detail_address' => $request->detail_address,
        ]);

        return back()->with('success', 'Cập nhật thông tin cá nhân và địa chỉ mặc định thành công.');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'Mật khẩu hiện tại là bắt buộc.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'new_password.required' => 'Mật khẩu mới là bắt buộc.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công.');
    }
}
