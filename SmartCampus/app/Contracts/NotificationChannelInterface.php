<?php

namespace App\Contracts;

use App\Models\User;

interface NotificationChannelInterface
{
    /**
     * Mengirim notifikasi ke user tertentu.
     *
     * @param User $user
     * @param string $message
     * @param array $payload Data tambahan (misal: assignment_id)
     * @return bool Status pengiriman
     */
    public function send(User $user, string $message, array $payload = []): bool;
}
