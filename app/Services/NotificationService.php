<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;

class NotificationService
{
    /**
     * Send a notification to a specific user.
     */
    public function notify(User $user, string $title, string $content, string $type, ?string $link = null): Notification
    {
        // Avoid duplicate notification if status hasn't changed (e.g. within a short time)
        // Checked in caller or simple check here
        $existing = Notification::where('user_id', $user->id)
            ->where('title', $title)
            ->where('content', $content)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();

        if ($existing) {
            return $existing;
        }

        return Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'content' => $content,
            'link' => $link,
            'type' => $type,
        ]);
    }

    /**
     * Send a notification to all users (e.g. new product, new game/event).
     */
    public function notifyAll(string $title, string $content, string $type, ?string $link = null): void
    {
        $users = User::where('status', 'active')->get();
        foreach ($users as $user) {
            $this->notify($user, $title, $content, $type, $link);
        }
    }
}
