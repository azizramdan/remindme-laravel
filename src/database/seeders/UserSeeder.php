<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::query()->exists()) {
            return;
        }

        $password = bcrypt('123456');

        User::factory()->create([
            'name' => 'Alice',
            'email' => 'alice@mail.com',
            'password' => $password,
        ]);

        User::factory()->create([
            'name' => 'Bob',
            'email' => 'bob@mail.com',
            'password' => $password,
        ]);
    }
}
