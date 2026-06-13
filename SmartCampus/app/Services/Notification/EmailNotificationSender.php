<?php

namespace App\Services\Notification;

use App\Contracts\NotificationChannelInterface;

class EmailNotificationSender extends NotificationSender
{
    protected function createChannel(): NotificationChannelInterface
    {
        return new EmailChannel();
    }
}
