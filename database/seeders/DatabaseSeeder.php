<?php

namespace Database\Seeders;

use App\Jobs\GenerateTaskAISummaryJob;
use App\Models\Task;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        Task::factory()
            ->count(10)
            ->create(['assigned_to' => $user->id])
            ->each(fn (Task $task) => GenerateTaskAISummaryJob::dispatch($task->id));
    }
}
