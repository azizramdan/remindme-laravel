<?php

namespace Tests\Feature;

use App\Enums\CommonError;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestCantGetListReminder()
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

    public function testGuestCantCreateReminder()
    {
        $this->postJson('/api/reminders')->assertUnauthorized();
    }

    #[DataProvider('invalidCreateRequestProvider')]
    public function testUserCantCreateReminderWithInvalidRequestBody(array $body)
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/api/reminders', $body)
            ->assertBadRequest()
            ->assertJsonFragment([
                'ok' => false,
                'err' => CommonError::ERR_BAD_REQUEST,
            ]);
    }

    public static function invalidCreateRequestProvider()
    {
        return [
            [
                [
                    // remind_at should be greater than now
                    'title' => 'foo',
                    'description' => 'bar',
                    'remind_at' => now()->subDay()->getTimestamp(),
                    'event_at' => now()->getTimestamp(),
                ],
            ],
            [
                [
                    // event_at should be greater than remind_at
                    'title' => 'foo',
                    'description' => 'bar',
                    'remind_at' => now()->addDay()->getTimestamp(),
                    'event_at' => now()->getTimestamp(),
                ],
            ],
            [
                [
                    // remind_at and event_at should be unix timestamp
                    'title' => 'foo',
                    'description' => 'bar',
                    'remind_at' => now()->addDay()->toDateTimeString(),
                    'event_at' => now()->addDay(2)->toDateTimeString(),
                ],
            ],
        ];
    }

    // user can create reminder
    public function testUserCanCreateReminder()
    {
        $remindAt = now()->addDay()->getTimestamp();
        $eventAt = now()->addDay(2)->getTimestamp();

        $this->actingAs(User::factory()->create())
            ->postJson('/api/reminders', [
                'title' => 'foo',
                'description' => 'bar',
                'remind_at' => $remindAt,
                'event_at' => $eventAt,
            ])
            ->assertOk()
            ->assertJsonFragment([
                'ok' => true,
                'data' => [
                    'id' => 1,
                    'title' => 'foo',
                    'description' => 'bar',
                    'remind_at' => $remindAt,
                    'event_at' => $eventAt,
                ],
            ]);
    }

    public function testGuestCantViewReminder()
    {
        $this->getJson('/api/reminders/1')->assertUnauthorized();
    }

    public function testUsersCanOnlyViewTheirReminder()
    {
        $reminder1 = Reminder::factory()->create();
        $reminder2 = Reminder::factory()->create();

        $this->actingAs($reminder1->user)
            ->getJson('/api/reminders/'.$reminder2->id)
            ->assertNotFound();
    }

    public function testUserCanOnlyViewUpcomingReminder()
    {
        $reminder = Reminder::factory()->create([
            'sent_at' => now()->getTimestamp(),
        ]);

        $this->actingAs($reminder->user)
            ->getJson('/api/reminders/'.$reminder->id)
            ->assertNotFound();
    }

    public function testUserCanViewReminder()
    {
        $reminder = Reminder::factory()->create();

        $this->actingAs($reminder->user)
            ->getJson('/api/reminders/'.$reminder->id)
            ->assertOk()
            ->assertJsonFragment([
                'ok' => true,
                'data' => [
                    'id' => $reminder->id,
                    'title' => $reminder->title,
                    'description' => $reminder->description,
                    'remind_at' => $reminder->remind_at,
                    'event_at' => $reminder->event_at,
                ],
            ]);
    }

    public function testGuestCantUpdateReminder()
    {
        $this->putJson('/api/reminders/1')->assertUnauthorized();
    }

    #[DataProvider('invalidUpdateRequestProvider')]
    public function testUserCantUpdateReminderWithInvalidRequestBody(array $body)
    {
        $reminder = Reminder::factory()->create([
            'remind_at' => now()->addHours(1)->getTimestamp(),
            'event_at' => now()->addHours(2)->getTimestamp(),
        ]);

        $this->actingAs($reminder->user)
            ->putJson('/api/reminders/'.$reminder->id, $body)
            ->assertBadRequest()
            ->assertJsonFragment([
                'ok' => false,
                'err' => CommonError::ERR_BAD_REQUEST,
            ]);
    }

    public static function invalidUpdateRequestProvider()
    {
        return [
            [
                [
                    // remind_at should be greater than now
                    'remind_at' => now()->subHour()->getTimestamp(),
                ],
            ],
            [
                [
                    // event_at should be greater than remind_at
                    'event_at' => now()->getTimestamp(),
                ],
            ],
            [
                [
                    // remind_at should be unix timestamp
                    'remind_at' => now()->addHours(1)->toDateTimeString(),
                ],
            ],
            [
                [
                    // event_at should be unix timestamp
                    'event_at' => now()->addHours(2)->toDateTimeString(),
                ],
            ],
        ];
    }

    public function testUsersCanOnlyUpdateTheirReminder()
    {
        $reminder1 = Reminder::factory()->create();
        $reminder2 = Reminder::factory()->create();

        $this->actingAs($reminder1->user)
            ->putJson('/api/reminders/'.$reminder2->id)
            ->assertNotFound();
    }

    public function testUserCanOnlyUpdateUpcomingReminder()
    {
        $reminder = Reminder::factory()->create([
            'sent_at' => now()->getTimestamp(),
        ]);

        $this->actingAs($reminder->user)
            ->putJson('/api/reminders/'.$reminder->id)
            ->assertNotFound();
    }

    #[DataProvider('validUpdateRequestProvider')]
    public function testUserCanUpdateReminder(array $body)
    {
        $reminder = Reminder::factory()->create([
            'remind_at' => now()->addHours(1)->getTimestamp(),
            'event_at' => now()->addHours(2)->getTimestamp(),
        ]);

        $response = $this->actingAs($reminder->user)
            ->putJson('/api/reminders/'.$reminder->id, $body)
            ->assertOk()
            ->assertJsonStructure([
                'ok',
                'data' => ['id', 'title', 'description', 'remind_at', 'event_at'],
            ]);

        foreach ($response->json()['data'] as $key => $value) {
            if (isset($body[$key])) {
                $this->assertNotSame($value, $reminder->{$key});
                $this->assertSame($body[$key], $value);
            } else {
                $this->assertSame($value, $reminder->{$key});
            }
        }
    }

    public static function validUpdateRequestProvider()
    {
        return [
            [
                [
                    'remind_at' => now()->addHours(1)->addMinutes(30)->getTimestamp(),
                ],
            ],
            [
                [
                    'event_at' => now()->addHours(2)->addMinutes(30)->getTimestamp(),
                ],
            ],
            [
                [
                    'title' => 'foo',
                ],
            ],
            [
                [
                    'description' => 'bar',
                ],
            ],
        ];
    }

    public function testGuestCantDeleteReminder()
    {
        $this->deleteJson('/api/reminders/1')->assertUnauthorized();
    }

    public function testUsersCanOnlyDeleteTheirReminder()
    {
        $reminder1 = Reminder::factory()->create();
        $reminder2 = Reminder::factory()->create();

        $this->actingAs($reminder1->user)
            ->deleteJson('/api/reminders/'.$reminder2->id)
            ->assertNotFound();
    }

    public function testUserCanOnlyDeleteUpcomingReminder()
    {
        $reminder = Reminder::factory()->create([
            'sent_at' => now()->getTimestamp(),
        ]);

        $this->actingAs($reminder->user)
            ->deleteJson('/api/reminders/'.$reminder->id)
            ->assertNotFound();
    }

    public function testUserCanDeleteReminder()
    {
        $reminder = Reminder::factory()->create();

        $this->actingAs($reminder->user)
            ->deleteJson('/api/reminders/'.$reminder->id)
            ->assertOk();

        $this->assertNull(Reminder::query()->find($reminder->id));
    }
}
