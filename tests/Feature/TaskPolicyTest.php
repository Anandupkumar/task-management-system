<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user1;
    protected User $user2;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->admin()->create();
        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
    }

    public function test_admin_can_view_all_tasks()
    {
        Task::factory()->create(['assigned_to' => $this->user1->id]);
        Task::factory()->create(['assigned_to' => $this->user2->id]);
        
        $response = $this->actingAs($this->admin)->get('/tasks');
        $response->assertStatus(200);
        $this->assertCount(2, $response->viewData('tasks'));
    }

    public function test_user_can_only_view_assigned_tasks()
    {
        Task::factory()->create(['assigned_to' => $this->user1->id]);
        Task::factory()->create(['assigned_to' => $this->user2->id]);
        
        $response = $this->actingAs($this->user1)->get('/tasks');
        $response->assertStatus(200);
        
        $tasks = $response->viewData('tasks');
        $this->assertCount(1, $tasks);
        $this->assertEquals($this->user1->id, $tasks->first()->assigned_to);
    }

    public function test_user_cannot_create_update_or_delete_tasks()
    {
        $task = Task::factory()->create(['assigned_to' => $this->user1->id]);
        
        $this->actingAs($this->user1)->get('/tasks/create')->assertStatus(403);
        $this->actingAs($this->user1)->get("/tasks/{$task->id}/edit")->assertStatus(403);
        $this->actingAs($this->user1)->delete("/tasks/{$task->id}")->assertStatus(403);
    }
}
