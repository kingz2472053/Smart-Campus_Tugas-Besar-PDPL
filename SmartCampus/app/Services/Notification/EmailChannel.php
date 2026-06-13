<?php

namespace App\Services\Notification;

use App\Contracts\NotificationChannelInterface;
use App\Models\User;
use App\Models\Notification;
use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailChannel implements NotificationChannelInterface
{
    public function send(User $user, string $message, array $payload = []): bool
    {
        try {
            // Catat log pengiriman email di database notifications
            Notification::create([
                'user_id' => $user->id,
                'assignment_id' => $payload['assignment_id'] ?? null,
                'channel' => 'email',
                'message' => $message,
                'is_read' => true, // Email selalu dianggap terbaca secara internal
                'sent_at' => now(),
            ]);

            // Kirim email riil menggunakan Mailable Laravel
            Mail::to($user->email)->send(new NotificationMail($user, $message));
            
            Log::info("Email notification successfully sent to {$user->email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send email notification to {$user->email}: " . $e->getMessage());
            return false; 
        }
    }
}
