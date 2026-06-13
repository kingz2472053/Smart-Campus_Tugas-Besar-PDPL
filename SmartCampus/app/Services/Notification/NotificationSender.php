<?php

namespace App\Services\Notification;

use App\Contracts\NotificationChannelInterface;
use App\Models\User;

abstract class NotificationSender
{
    /**
     * Factory Method untuk instansiasi channel notifikasi.
     *
     * @return NotificationChannelInterface
     */
    abstract protected function createChannel(): NotificationChannelInterface;

    /**
     * Mengirim notifikasi menggunakan channel yang diciptakan oleh subclass.
     *
     * @param User $user
     * @param string $message
     * @param array $payload
     * @return bool
     */
    public function sendNotification(User $user, string $message, array $payload = []): bool
    {
        $channel = $this->createChannel();
        return $channel->send($user, $message, $payload);
    }
}
