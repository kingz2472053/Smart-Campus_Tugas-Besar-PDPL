<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Notification;
use App\Services\Notification\DashboardNotificationSender;
use App\Services\Notification\EmailNotificationSender;
use App\Services\Notification\DashboardChannel;
use App\Services\Notification\EmailChannel;
use App\Mail\NotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationMultiChannelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test whether Creators produce correct Product instances (Factory Method).
     */
    public function test_factory_method_creates_correct_channels(): void
    {
        $dashboardSender = new DashboardNotificationSender();
        $emailSender = new EmailNotificationSender();

        // Reflection to inspect protected createChannel() method
        $reflectionDashboard = new \ReflectionClass(DashboardNotificationSender::class);
        $methodDashboard = $reflectionDashboard->getMethod('createChannel');
        $methodDashboard->setAccessible(true);
        $dashboardChannel = $methodDashboard->invoke($dashboardSender);

        $reflectionEmail = new \ReflectionClass(EmailNotificationSender::class);
        $methodEmail = $reflectionEmail->getMethod('createChannel');
        $methodEmail->setAccessible(true);
        $emailChannel = $methodEmail->invoke($emailSender);

        $this->assertInstanceOf(DashboardChannel::class, $dashboardChannel);
        $this->assertInstanceOf(EmailChannel::class, $emailChannel);
    }

    /**
     * Test DashboardNotificationSender saves notification correctly in DB.
     */
    public function test_dashboard_notification_sender_saves_to_database(): void
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa'
        ]);

        $sender = new DashboardNotificationSender();
        $message = "Ini adalah contoh notifikasi dashboard.";
        
        $result = $sender->sendNotification($user, $message);
        
        $this->assertTrue($result);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'channel' => 'dashboard',
            'message' => $message,
            'is_read' => false,
        ]);
    }

    /**
     * Test EmailNotificationSender logs and sends emails.
     */
    public function test_email_notification_sender_sends_email_and_logs(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'email' => 'mahasiswa@smartcampus.id'
        ]);

        $sender = new EmailNotificationSender();
        $message = "Ini adalah contoh notifikasi email.";

        $result = $sender->sendNotification($user, $message);

        $this->assertTrue($result);
        
        // Assert email was sent via Mail::fake
        Mail::assertSent(NotificationMail::class, function ($mail) use ($user, $message) {
            return $mail->hasTo($user->email) && 
                   $mail->user->id === $user->id &&
                   $mail->notificationMessage === $message;
        });

        // Assert notification logged in the DB
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'channel' => 'email',
            'message' => $message,
            'is_read' => true,
        ]);
    }
}
