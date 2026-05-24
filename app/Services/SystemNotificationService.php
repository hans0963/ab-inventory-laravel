<?php

namespace App\Services;

use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class SystemNotificationService
{
    public static function notifyRoles(array $roles, string $type, string $title, string $message, ?string $actionUrl = null): void
    {
        User::whereIn('role', $roles)->get()->each(function (User $user) use ($type, $title, $message, $actionUrl) {
            self::notifyUser($user->id, $type, $title, $message, $actionUrl);
        });
    }

    public static function notifyUser(?int $userId, string $type, string $title, string $message, ?string $actionUrl = null): void
    {
        if (! $userId) {
            return;
        }

        SystemNotification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_url' => $actionUrl,
        ]);

        Cache::forget('nav-notifications-' . $userId);
    }
}
