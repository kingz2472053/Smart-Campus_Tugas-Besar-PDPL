<?php

namespace App\Services\Notification;

use App\Contracts\NotificationChannelInterface;

class DashboardNotificationSender extends NotificationSender
{
    protected function createChannel(): NotificationChannelInterface
    {
        return new DashboardChannel();
    }
}
