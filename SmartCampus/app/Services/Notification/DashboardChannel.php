<?php

namespace App\Services\Notification;

use App\Contracts\NotificationChannelInterface;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class DashboardChannel implements NotificationChannelInterface
{
    public function send(User $user, string $message, array $payload = []): bool
    {
        try {
            Notification::create([
                'user_id' => $user->id,
                'assignment_id' => $payload['assignment_id'] ?? null,
                'channel' => 'dashboard',
                'message' => $message,
                'is_read' => false,
                'sent_at' => now(),
            ]);

            Log::info("Dashboard notification saved for User ID: {$user->id}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to save dashboard notification for User ID {$user->id}: " . $e->getMessage());
            return false;
        }
    }
}
