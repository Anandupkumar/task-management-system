<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected Task $task;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->create();
        
        $this->task = Task::factory()->create([
            'assigned_to' => $this->user->id,
            'ai_summary'  => 'Test AI Summary',
        ]);
    }

    public function test_api_returns_paginated_collection()
    {
        $response = $this->actingAs($this->admin)->getJson('/api/tasks');
        
        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_admin_can_create_task()
    {
        $data = [
            'title'       => 'New Task API',
            'priority'    => 'high',
            'assigned_to' => $this->user->id,
        ];
        
        $response = $this->actingAs($this->admin)->postJson('/api/tasks', $data);
        
        $response->assertStatus(201)
                 ->assertJsonPath('title', 'New Task API');
    }

    public function test_user_cannot_create_task()
    {
        $data = [
            'title'       => 'New Task API',
            'priority'    => 'high',
            'assigned_to' => $this->user->id,
        ];
        
        $response = $this->actingAs($this->user)->postJson('/api/tasks', $data);
        
        $response->assertStatus(403);
    }

    public function test_assigned_user_can_update_status()
    {
        $response = $this->actingAs($this->user)->patchJson("/api/tasks/{$this->task->id}/status", [
            'status' => 'in_progress',
        ]);
        
        $response->assertStatus(200)
                 ->assertJsonPath('status', 'in_progress');
    }

    public function test_ai_summary_endpoint_returns_db_values()
    {
        $response = $this->actingAs($this->user)->getJson("/api/tasks/{$this->task->id}/ai-summary");
        
        $response->assertStatus(200)
                 ->assertJson([
                     'ai_summary'  => 'Test AI Summary',
                     'ai_priority' => $this->task->ai_priority?->value,
                 ]);
    }
}
