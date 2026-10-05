<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    /**
     * Permanent Light mode handler.
     */
    public function toggleTheme(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $settings = UserSetting::firstOrCreate(
                ['user_id' => $user->id],
                ['theme' => 'light', 'notifications_enabled' => true, 'friend_system_enabled' => true]
            );
            $settings->update(['theme' => 'light']);
        }

        // Always store light theme in cookie
        Cookie::queue('theme', 'light', 60 * 24 * 365); // 1 year

        return response()->json([
            'success' => true,
            'theme' => 'light',
        ]);
    }

    /**
     * Update notification & friend system preferences.
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'notifications_enabled' => 'nullable|boolean',
            'friend_system_enabled' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $settings = UserSetting::firstOrCreate(
            ['user_id' => $user->id],
            ['theme' => 'light', 'notifications_enabled' => true, 'friend_system_enabled' => true]
        );

        $settings->update([
            'notifications_enabled' => $request->has('notifications_enabled'),
            'friend_system_enabled' => $request->has('friend_system_enabled'),
        ]);

        return back()->with('success', 'Cài đặt tài khoản đã được cập nhật thành công.');
    }

    /**
     * Store a report (product, review, comment, system issue).
     */
    public function fileReport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:product,review,comment,system',
            'target_id' => 'nullable|integer',
            'reason' => 'required|string|max:255',
            'content' => 'required|string',
        ], [
            'reason.required' => 'Lý do báo cáo là bắt buộc.',
            'content.required' => 'Nội dung chi tiết là bắt buộc.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                Report::create([
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'type' => $request->type,
                    'target_id' => $request->target_id,
                    'reason' => $request->reason,
                    'content' => $request->content,
                    'status' => 'pending',
                ]);
            });

            return back()->with('success', 'Báo cáo của bạn đã được tiếp nhận. Cảm ơn phản hồi từ bạn.');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
