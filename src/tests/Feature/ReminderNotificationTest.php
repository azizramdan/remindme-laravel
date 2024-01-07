<?php

namespace Tests\Feature;

use App\Jobs\SendReminder;
use App\Models\Reminder;
use App\Notifications\Reminder as NotificationsReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ReminderNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function testNotificationCanBeSent()
    {
        Notification::fake();

        $reminder = Reminder::factory()->create();

        $reminder->user->notify(new NotificationsReminder($reminder));

        Notification::assertSentTo(
            [$reminder->user],
            function (NotificationsReminder $notification, array $channels) use ($reminder) {
                $this->assertContains('mail', $channels);

                $mailNotification = (object) $notification->toMail($reminder->user);

                $this->assertEquals("Reminder: Upcoming Event - {$reminder->title}", $mailNotification->subject);

                return true;
            }
        );
    }

    public function testSendReminderCanBeDispatched()
    {
        Queue::fake();

        $reminder = Reminder::factory()->create();
        SendReminder::dispatch($reminder);

        Queue::assertPushed(SendReminder::class, 1);
    }

    public function testNotificationCanBeSentViaJob()
    {
        Notification::fake();

        $reminder = Reminder::factory()->create();

        SendReminder::dispatch($reminder);

        Notification::assertSentTo(
            [$reminder->user], NotificationsReminder::class
        );
    }

    public function testNotificationCanBeSentViaSendReminderCommand()
    {
        Notification::fake();

        $reminder = Reminder::factory()->create([
            'remind_at' => now()->subHour()->getTimestamp(),
        ]);

        $this->artisan('app:send-reminder')->assertSuccessful();

        Notification::assertSentTo(
            [$reminder->user], NotificationsReminder::class
        );
    }

    public function testNotifyUnsentRemindersOnly()
    {
        Notification::fake();

        Reminder::factory()->create([
            'remind_at' => now()->subHour()->getTimestamp(),
            'sent_at' => now()->getTimestamp(),
        ]);

        $this->artisan('app:send-reminder')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function testNotifyOverdueRemindersOnly()
    {
        Notification::fake();

        Reminder::factory()->create();

        $this->artisan('app:send-reminder')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function testNotificationNoOverlap()
    {
        Notification::fake();

        Reminder::factory()->create([
            'remind_at' => now()->subHour()->getTimestamp(),
        ]);

        $this->artisan('app:send-reminder')->assertSuccessful();
        $this->artisan('app:send-reminder')->assertSuccessful();

        Notification::assertCount(1);
    }
}
