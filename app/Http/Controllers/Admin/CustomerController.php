<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer');

        if ($request->filled('q')) {
            $q = trim($request->q);
            if (mb_strlen($q, 'UTF-8') === 1) {
                // Gõ 1 ký tự: Chỉ tìm các từ bắt đầu bằng ký tự này
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "{$q}%")
                        ->orWhere('name', 'like', "% {$q}%")
                        ->orWhere('email', 'like', "{$q}%")
                        ->orWhere('phone', 'like', "{$q}%")
                        ->orWhere('customer_code', 'like', "{$q}%");
                });
            } else {
                // Gõ >= 2 ký tự: Tìm linh hoạt ở mọi vị trí
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('customer_code', 'like', "%{$q}%");
                });
            }
        }

        $customers = $query->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = User::where('role', 'customer')->with(['orders' => function ($q) {
            $q->latest();
        }])->findOrFail($id);

        return view('admin.customers.show', compact('customer'));
    }

    public function toggleStatus($id)
    {
        $customer = User::findOrFail($id);

        if ($customer->id === Auth::id()) {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình.');
        }

        $newStatus = $customer->status === 'active' ? 'locked' : 'active';
        $customer->update(['status' => $newStatus]);

        $message = $newStatus === 'locked' ? 'Đã khóa tài khoản khách hàng.' : 'Đã mở khóa tài khoản khách hàng.';

        return back()->with('success', $message);
    }

    public function destroy($id)
    {
        $customer = User::findOrFail($id);

        if ($customer->id === Auth::id()) {
            return back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình.');
        }

        if ($customer->role !== 'customer') {
            return back()->with('error', 'Chỉ có thể xóa tài khoản của khách hàng.');
        }

        $customerName = $customer->name ?? $customer->username ?? 'Khách hàng #' . $customer->id;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa các dữ liệu liên quan đến khách hàng
        DB::table('carts')->where('user_id', $customer->id)->delete();
        DB::table('daily_checkins')->where('user_id', $customer->id)->delete();
        DB::table('friendships')->where('user_id', $customer->id)->orWhere('friend_id', $customer->id)->delete();
        DB::table('game_attempts')->where('user_id', $customer->id)->delete();
        DB::table('notifications')->where('user_id', $customer->id)->delete();
        DB::table('point_transactions')->where('user_id', $customer->id)->delete();
        DB::table('product_reviews')->where('user_id', $customer->id)->delete();
        DB::table('product_views')->where('user_id', $customer->id)->delete();
        DB::table('reports')->where('user_id', $customer->id)->delete();
        DB::table('review_votes')->where('user_id', $customer->id)->delete();
        DB::table('reward_redemptions')->where('user_id', $customer->id)->delete();
        DB::table('sales_schedules')->where('user_id', $customer->id)->delete();
        DB::table('sessions')->where('user_id', $customer->id)->delete();
        DB::table('user_settings')->where('user_id', $customer->id)->delete();
        DB::table('user_vouchers')->where('user_id', $customer->id)->delete();
        DB::table('wishlists')->where('user_id', $customer->id)->delete();
        DB::table('orders')->where('user_id', $customer->id)->delete();

        // Xóa khách hàng
        $customer->delete();

        // Đánh lại số thứ tự ID liên tục và reset AUTO_INCREMENT chuẩn thứ tự
        $this->reorderUserIds();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return redirect()->route('admin.customers.index')
            ->with('success', "Đã xóa tài khoản \"{$customerName}\" thành công và cập nhật lại thứ tự ID trong CSDL.");
    }

    /**
     * Sắp xếp và đánh lại ID bảng users từ 1..N liên tục, đồng thời reset AUTO_INCREMENT
     */
    protected function reorderUserIds(): void
    {
        $relatedTables = [
            ['table' => 'carts', 'column' => 'user_id'],
            ['table' => 'daily_checkins', 'column' => 'user_id'],
            ['table' => 'friendships', 'column' => 'user_id'],
            ['table' => 'friendships', 'column' => 'friend_id'],
            ['table' => 'game_attempts', 'column' => 'user_id'],
            ['table' => 'notifications', 'column' => 'user_id'],
            ['table' => 'orders', 'column' => 'user_id'],
            ['table' => 'point_transactions', 'column' => 'user_id'],
            ['table' => 'product_reviews', 'column' => 'user_id'],
            ['table' => 'product_views', 'column' => 'user_id'],
            ['table' => 'reports', 'column' => 'user_id'],
            ['table' => 'review_votes', 'column' => 'user_id'],
            ['table' => 'reward_redemptions', 'column' => 'user_id'],
            ['table' => 'sales_schedules', 'column' => 'user_id'],
            ['table' => 'sessions', 'column' => 'user_id'],
            ['table' => 'user_settings', 'column' => 'user_id'],
            ['table' => 'user_vouchers', 'column' => 'user_id'],
            ['table' => 'wishlists', 'column' => 'user_id'],
        ];

        $users = DB::table('users')->orderBy('id', 'asc')->get();

        $newId = 1;
        foreach ($users as $user) {
            $oldId = $user->id;
            if ($oldId !== $newId) {
                DB::table('users')->where('id', $oldId)->update(['id' => $newId]);
                foreach ($relatedTables as $rel) {
                    DB::table($rel['table'])->where($rel['column'], $oldId)->update([$rel['column'] => $newId]);
                }
            }
            $newId++;
        }

        $maxId = DB::table('users')->max('id') ?? 0;
        $nextAutoInc = $maxId + 1;
        DB::statement("ALTER TABLE users AUTO_INCREMENT = {$nextAutoInc};");
    }
}
