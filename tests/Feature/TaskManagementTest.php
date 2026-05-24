<?php

namespace Tests\Feature;

use App\Jobs\GenerateTaskAISummaryJob;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_task_dispatches_ai_job()
    {
        Queue::fake();
        
        $admin = User::factory()->admin()->create();
        
        $response = $this->actingAs($admin)->post('/tasks', [
            'title'       => 'Test Dispatch',
            'priority'    => 'medium',
            'assigned_to' => $admin->id,
        ]);
        
        $response->assertRedirect('/tasks');
        
        Queue::assertPushed(GenerateTaskAISummaryJob::class, function ($job) {
            return Task::find($job->taskId)->title === 'Test Dispatch';
        });
    }

    public function test_update_and_delete_tasks()
    {
        $admin = User::factory()->admin()->create();
        $task = Task::factory()->create(['assigned_to' => $admin->id, 'title' => 'Old Title']);
        
        // Update
        $this->actingAs($admin)->put("/tasks/{$task->id}", [
            'title' => 'New Title',
        ])->assertRedirect('/tasks');
        
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'New Title']);
        
        // Delete
        $this->actingAs($admin)->delete("/tasks/{$task->id}")->assertRedirect('/tasks');
        
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
