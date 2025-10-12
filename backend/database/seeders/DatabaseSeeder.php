<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Matt Recruiter',
            'email' => 'matt@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->call(TaskSeeder::class);
    }
}
