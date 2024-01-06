<?php

namespace Tests\Feature;

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestCantAccessReminder()
    {
        $this->getJson('/api/reminders')->assertUnauthorized();
    }

    public function testUsersCanOnlyRetrieveTheirReminderData()
    {
        Reminder::factory()->create();
        $reminder = Reminder::factory()->create();

        $this->assertSame(2, Reminder::count());

        $this->actingAs($reminder->user)
            ->getJson('/api/reminders')
            ->assertOk()
            ->assertJsonStructure([
                'ok',
                'data' => [
                    'reminders' => [
                        '*' => ['id', 'title', 'description', 'remind_at', 'event_at'],
                    ],
                    'limit',
                ],
            ])
            ->assertJsonCount(1, 'data.reminders');
    }

    // create test limit get list reminders
    public function testLimitGetListReminders()
    {
        $user = User::factory()
            ->has(Reminder::factory()->count(20))
            ->create();

        $this->actingAs($user);

        $this->getJson('/api/reminders')->assertJsonCount(10, 'data.reminders');

        $this->getJson('/api/reminders?limit=2')->assertJsonCount(2, 'data.reminders');
    }

    // test only get upcoming reminders
    public function testOnlyGetUpcomingReminders()
    {
        $user = User::factory()->create();

        Reminder::factory()
            ->for($user)
            ->create([
                'sent_at' => now()->getTimestamp(),
            ]);
        Reminder::factory()->for($user)->create();

        $this->actingAs($user)
            ->getJson('/api/reminders')
            ->assertJsonCount(1, 'data.reminders');
    }

    public function testGetListSortedByRemindAt()
    {
        $user = User::factory()->create();

        Reminder::factory()
            ->for($user)
            ->create([
                'remind_at' => now()->addHour(2)->getTimestamp(),
            ]);
        Reminder::factory()
            ->for($user)
            ->create([
                'remind_at' => now()->addHour(1)->getTimestamp(),
            ]);

        $this->actingAs($user)
            ->getJson('/api/reminders')
            ->assertJsonPath('data.reminders.0.id', 2)
            ->assertJsonPath('data.reminders.1.id', 1);
    }
}
