<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reminder>
 */
class ReminderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventAt = fake()->dateTimeBetween('+1 day', '+1 year');

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(2),
            'description' => fake()->text(),
            'remind_at' => fake()->dateTimeBetween('+1 hour', $eventAt)->getTimestamp(),
            'event_at' => $eventAt->getTimestamp(),
        ];
    }
}
