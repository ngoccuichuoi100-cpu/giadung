<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\User;
use App\Models\UserVoucher;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;

class VoucherController extends Controller
{
    protected $notificationService;
    protected $isUnderMaintenance = true; // Đang tạm dừng để nâng cấp tính năng

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Danh sách mã ưu đãi & thống kê
     */
    public function index(Request $request)
    {
        $query = Voucher::query();

        // Search code, name
        if ($request->filled('q')) {
            $q = trim($request->q);
            if (mb_strlen($q, 'UTF-8') === 1) {
                // Gõ 1 ký tự: Chỉ tìm các từ bắt đầu bằng ký tự này hoặc mã code
                $query->where(function ($sub) use ($q) {
                    $sub->where('code', 'like', "{$q}%")
                        ->orWhere('name', 'like', "{$q}%")
                        ->orWhere('name', 'like', "% {$q}%");
                });
            } else {
                // Gõ >= 2 ký tự: Tìm linh hoạt ở mọi vị trí
                $query->where(function ($sub) use ($q) {
                    $sub->where('code', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            }
        }

        // Filter type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter acquisition type
        if ($request->filled('acquisition_type')) {
            $query->where('acquisition_type', $request->acquisition_type);
        }

        // Filter status
        if ($request->filled('status')) {
            if ($request->status === 'expired') {
                $query->where('end_date', '<', now());
            } elseif ($request->status === 'upcoming') {
                $query->where('start_date', '>', now());
            } else {
                $query->where('status', $request->status)
                      ->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
            }
        }

        $vouchers = $query->withCount('userVouchers')->latest('id')->paginate(20)->withQueryString();

        // Statistics
        $totalVouchers = Voucher::count();
        $activeVouchers = Voucher::where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();
        $totalUses = Voucher::sum('uses');
        $memberVouchers = Voucher::where('acquisition_type', 'new_member')->count();

        // Get customers list for gifting modal
        $customers = User::where('role', 'customer')->orderBy('name')->select('id', 'name', 'email', 'customer_code')->get();

        return view('admin.vouchers.index', compact(
            'vouchers',
            'totalVouchers',
            'activeVouchers',
            'totalUses',
            'memberVouchers',
            'customers'
        ));
    }

    /**
     * Trang thêm mã ưu đãi mới
     */
    public function create()
    {
        if ($this->isUnderMaintenance) {
            return redirect()->route('admin.vouchers.index')->with('info', 'Tính năng này đang được nâng cấp để mang lại trải nghiệm tốt hơn. Vui lòng quay lại sau nhé!');
        }

        return view('admin.vouchers.create');
    }

    /**
     * Lưu mã ưu đãi mới vào CSDL
     */
    public function store(Request $request)
    {
        if ($this->isUnderMaintenance) {
            return redirect()->route('admin.vouchers.index')->with('info', 'Tính năng này đang được nâng cấp để mang lại trải nghiệm tốt hơn. Vui lòng quay lại sau nhé!');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:fixed,percent,free_shipping',
            'acquisition_type' => 'required|string|in:public,new_member,game_reward,referral,loyalty_points,special_gift',
            'value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:active,inactive',
        ], [
            'code.required' => 'Mã ưu đãi là bắt buộc.',
            'code.unique' => 'Mã ưu đãi này đã tồn tại trong hệ thống.',
            'name.required' => 'Tên chương trình ưu đãi là bắt buộc.',
            'type.in' => 'Loại ưu đãi không hợp lệ.',
            'acquisition_type.in' => 'Cách nhận mã không hợp lệ.',
            'value.required' => 'Giá trị ưu đãi là bắt buộc.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải diễn ra sau hoặc cùng ngày bắt đầu.',
        ]);

        // Code luôn viết hoa, bỏ khoảng trắng
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['max_uses'] = $validated['max_uses'] ?? 0;
        $validated['min_order_value'] = $validated['min_order_value'] ?? 0;
        $validated['uses'] = 0;

        // Nếu là phần trăm thì giá trị không vượt quá 100%
        if ($validated['type'] === 'percent' && $validated['value'] > 100) {
            return back()->withInput()->with('error', 'Giá trị giảm theo phần trăm không được vượt quá 100%.');
        }

        $voucher = Voucher::create($validated);

        return redirect()->route('admin.vouchers.index')->with('success', "Đã tạo thành công mã ưu đãi {$voucher->code}!");
    }

    /**
     * Trang chỉnh sửa mã ưu đãi
     */
    public function edit($id)
    {
        if ($this->isUnderMaintenance) {
            return redirect()->route('admin.vouchers.index')->with('info', 'Tính năng này đang được nâng cấp để mang lại trải nghiệm tốt hơn. Vui lòng quay lại sau nhé!');
        }

        $voucher = Voucher::findOrFail($id);
        return view('admin.vouchers.edit', compact('voucher'));
    }

    /**
     * Cập nhật thông tin mã ưu đãi
     */
    public function update(Request $request, $id)
    {
        if ($this->isUnderMaintenance) {
            return redirect()->route('admin.vouchers.index')->with('info', 'Tính năng này đang được nâng cấp để mang lại trải nghiệm tốt hơn. Vui lòng quay lại sau nhé!');
        }

        $voucher = Voucher::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:fixed,percent,free_shipping',
            'acquisition_type' => 'required|string|in:public,new_member,game_reward,referral,loyalty_points,special_gift',
            'value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_uses' => 'nullable|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string|in:active,inactive',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['max_uses'] = $validated['max_uses'] ?? 0;
        $validated['min_order_value'] = $validated['min_order_value'] ?? 0;

        if ($validated['type'] === 'percent' && $validated['value'] > 100) {
            return back()->withInput()->with('error', 'Giá trị giảm theo phần trăm không được vượt quá 100%.');
        }

        $voucher->update($validated);

        return redirect()->route('admin.vouchers.index')->with('success', "Đã cập nhật mã ưu đãi {$voucher->code} thành công!");
    }

    /**
     * Xóa mã ưu đãi
     */
    public function destroy($id)
    {
        if ($this->isUnderMaintenance) {
            return redirect()->route('admin.vouchers.index')->with('info', 'Tính năng này đang được nâng cấp để mang lại trải nghiệm tốt hơn. Vui lòng quay lại sau nhé!');
        }

        $voucher = Voucher::findOrFail($id);
        $code = $voucher->code;
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')->with('success', "Đã xóa mã ưu đãi {$code} thành công.");
    }

    /**
     * Bật / Tắt trạng thái nhanh (active <-> inactive)
     */
    public function toggleStatus($id)
    {
        if ($this->isUnderMaintenance) {
            return redirect()->route('admin.vouchers.index')->with('info', 'Tính năng này đang được nâng cấp để mang lại trải nghiệm tốt hơn. Vui lòng quay lại sau nhé!');
        }

        $voucher = Voucher::findOrFail($id);
        $newStatus = $voucher->status === 'active' ? 'inactive' : 'active';
        $voucher->update(['status' => $newStatus]);

        $msg = $newStatus === 'active' ? "Đã kích hoạt mã {$voucher->code}." : "Đã tạm dừng mã {$voucher->code}.";
        return back()->with('success', $msg);
    }

    /**
     * Tặng mã ưu đãi trực tiếp cho khách hàng (Gán vào UserVoucher)
     */
    public function giveToCustomers(Request $request, $id)
    {
        if ($this->isUnderMaintenance) {
            return redirect()->route('admin.vouchers.index')->with('info', 'Tính năng này đang được nâng cấp để mang lại trải nghiệm tốt hơn. Vui lòng quay lại sau nhé!');
        }
        $voucher = Voucher::findOrFail($id);

        $request->validate([
            'target_type' => 'required|in:selected,all',
            'customer_ids' => 'required_if:target_type,selected|array',
            'customer_ids.*' => 'exists:users,id',
        ]);

        $targetUsers = collect();
        if ($request->target_type === 'all') {
            $targetUsers = User::where('role', 'customer')->get();
        } else {
            $targetUsers = User::whereIn('id', $request->customer_ids)->get();
        }

        if ($targetUsers->isEmpty()) {
            return back()->with('error', 'Chưa chọn khách hàng nào để tặng mã.');
        }

        $count = 0;
        foreach ($targetUsers as $user) {
            // Không tặng lặp nếu khách đã có mã này chưa dùng
            $exists = UserVoucher::where('user_id', $user->id)
                ->where('voucher_id', $voucher->id)
                ->whereNull('used_at')
                ->exists();

            if (!$exists) {
                UserVoucher::create([
                    'user_id' => $user->id,
                    'voucher_id' => $voucher->id,
                ]);

                // Gửi thông báo trong web cho khách
                $discountDesc = $voucher->type === 'free_shipping' ? 'Miễn phí vận chuyển' : ($voucher->type === 'percent' ? "giảm {$voucher->value}%" : "giảm " . number_format($voucher->value, 0, ',', '.') . " ₫");
                $this->notificationService->notify(
                    $user,
                    "Bạn nhận được mã ưu đãi đặc biệt!",
                    "GiaDụngPro vừa gửi tặng bạn mã ưu đãi {$voucher->code} ({$discountDesc}). Hãy kiểm tra kho voucher và sử dụng ngay nhé!",
                    "voucher",
                    route('profile.index')
                );

                $count++;
            }
        }

        return back()->with('success', "Đã gửi tặng mã ưu đãi {$voucher->code} thành công tới {$count} khách hàng.");
    }
}
