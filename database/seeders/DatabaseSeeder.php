<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
public function run(): void
{
    // Prevent duplicate user creation
    User::firstOrCreate(
        ['email' => 'test@example.com'],
        [
            'name' => 'Test User',
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // use a secure password
            'remember_token' => \Str::random(10),
        ]
    );

    // Seed levels
    $this->call([
        LevelsTableSeeder::class,
    ]);
    // Seed semesters
    $this->call([
        SemesterSeeder::class,
    ]);
}

}
