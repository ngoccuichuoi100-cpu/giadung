<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class FriendshipController extends Controller
{
    /**
     * Check if the friendship system is enabled.
     */
    protected function checkEnabled()
    {
        $user = Auth::user();
        if ($user->settings && !$user->settings->friend_system_enabled) {
            abort(403, 'Chức năng bạn bè hiện đang bị tắt trong cài đặt.');
        }
    }

    public function index()
    {
        $this->checkEnabled();
        $user = Auth::user();

        // Get friends (accepted relationships)
        $friends1 = Friendship::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->pluck('friend_id');
        $friends2 = Friendship::where('friend_id', $user->id)
            ->where('status', 'accepted')
            ->pluck('user_id');
        
        $friendIds = $friends1->merge($friends2)->unique();
        $friends = User::whereIn('id', $friendIds)->get();

        // Get pending requests (received)
        $pendingRequests = Friendship::where('friend_id', $user->id)
            ->where('status', 'pending')
            ->with('sender')
            ->get();

        // Get sent requests
        $sentRequests = Friendship::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('recipient')
            ->get();

        // Get blocked users
        $blockedIds = Friendship::where('user_id', $user->id)
            ->where('status', 'blocked')
            ->pluck('friend_id');
        $blockedUsers = User::whereIn('id', $blockedIds)->get();

        // Search for potential friends
        $potentialFriends = User::where('id', '!=', $user->id)
            ->where('role', 'customer')
            ->where('status', 'active')
            ->whereNotIn('id', $friendIds)
            ->whereNotIn('id', $blockedIds)
            ->limit(10)
            ->get();

        return view('friends.index', compact('friends', 'pendingRequests', 'sentRequests', 'blockedUsers', 'potentialFriends'));
    }

    public function sendRequest(Request $request)
    {
        $this->checkEnabled();
        $request->validate([
            'friend_id' => 'required|exists:users,id|not_in:' . Auth::id(),
        ]);

        $user = Auth::user();
        $friendId = $request->friend_id;

        // Check if there is an existing friendship
        $existing = Friendship::where(function ($q) use ($user, $friendId) {
            $q->where('user_id', $user->id)->where('friend_id', $friendId);
        })->orWhere(function ($q) use ($user, $friendId) {
            $q->where('user_id', $friendId)->where('friend_id', $user->id);
        })->first();

        if ($existing) {
            if ($existing->status === 'accepted') {
                return back()->with('error', 'Hai người đã là bạn bè.');
            }
            if ($existing->status === 'blocked') {
                return back()->with('error', 'Người dùng này đã bị chặn.');
            }
            return back()->with('error', 'Đã có yêu cầu kết bạn đang chờ xử lý.');
        }

        Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $friendId,
            'status' => 'pending',
        ]);

        // Send system notification to friend
        $notificationService = resolve(\App\Services\NotificationService::class);
        $notificationService->notify(
            User::find($friendId),
            "Lời mời kết bạn mới",
            "{$user->name} đã gửi cho bạn một lời mời kết bạn.",
            "system",
            route('friends.index')
        );

        return back()->with('success', 'Đã gửi lời mời kết bạn thành công.');
    }

    public function acceptRequest($id)
    {
        $this->checkEnabled();
        $user = Auth::user();
        $friendship = Friendship::where('friend_id', $user->id)
            ->where('user_id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->update(['status' => 'accepted']);

        // Notify sender
        $notificationService = resolve(\App\Services\NotificationService::class);
        $notificationService->notify(
            User::find($id),
            "Chấp nhận kết bạn",
            "{$user->name} đã chấp nhận lời mời kết bạn của bạn.",
            "system",
            route('friends.index')
        );

        return back()->with('success', 'Đã đồng ý kết bạn.');
    }

    public function declineRequest($id)
    {
        $this->checkEnabled();
        $user = Auth::user();
        $friendship = Friendship::where('friend_id', $user->id)
            ->where('user_id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->delete();

        return back()->with('success', 'Đã từ chối kết bạn.');
    }

    public function removeFriend($id)
    {
        $this->checkEnabled();
        $user = Auth::user();
        Friendship::where('status', 'accepted')
            ->where(function ($q) use ($user, $id) {
                $q->where(function ($sub) use ($user, $id) {
                    $sub->where('user_id', $user->id)->where('friend_id', $id);
                })->orWhere(function ($sub) use ($user, $id) {
                    $sub->where('user_id', $id)->where('friend_id', $user->id);
                });
            })->delete();

        return back()->with('success', 'Đã hủy kết bạn.');
    }

    public function blockUser(Request $request)
    {
        $this->checkEnabled();
        $request->validate([
            'user_id' => 'required|exists:users,id|not_in:' . Auth::id(),
        ]);

        $user = Auth::user();
        $blockId = $request->user_id;

        // Delete any existing friendship first
        Friendship::where(function ($q) use ($user, $blockId) {
            $q->where('user_id', $user->id)->where('friend_id', $blockId);
        })->orWhere(function ($q) use ($user, $blockId) {
            $q->where('user_id', $blockId)->where('friend_id', $user->id);
        })->delete();

        // Create block record
        Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $blockId,
            'status' => 'blocked',
        ]);

        return back()->with('success', 'Đã chặn người dùng này thành công.');
    }

    public function unblockUser($id)
    {
        $this->checkEnabled();
        $user = Auth::user();
        Friendship::where('user_id', $user->id)
            ->where('friend_id', $id)
            ->where('status', 'blocked')
            ->delete();

        return back()->with('success', 'Đã bỏ chặn người dùng này.');
    }
}
