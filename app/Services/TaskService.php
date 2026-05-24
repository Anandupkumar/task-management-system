<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Jobs\GenerateTaskAISummaryJob;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(
        protected TaskRepositoryInterface $taskRepository
    ) {}

    public function getAllTasks(array $filters, User $user)
    {
        return $this->taskRepository->all($filters, $user);
    }

    public function createTask(array $data): Task
    {
        return DB::transaction(function () use ($data) {
            $task = $this->taskRepository->create($data);
            GenerateTaskAISummaryJob::dispatch($task->id);
            return $task;
        });
    }

    public function updateTask(int $id, array $data): Task
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->taskRepository->update($id, $data);
        });
    }

    public function updateTaskStatus(int $id, string $status): Task
    {
        return DB::transaction(function () use ($id, $status) {
            return $this->taskRepository->update($id, ['status' => $status]);
        });
    }

    public function deleteTask(int $id): void
    {
        DB::transaction(function () use ($id) {
            $this->taskRepository->delete($id);
        });
    }

    public function getTaskForAISummary(int $id): Task
    {
        return $this->taskRepository->find($id);
    }
    
    public function getDashboardStats(?User $user = null): array
    {
        $query = Task::query();
        
        if ($user && !$user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }
        
        $total = (clone $query)->count();
        $completed = (clone $query)->where('status', 'completed')->count();
        $pending = (clone $query)->where('status', 'pending')->count();
        $highPriority = (clone $query)->where('priority', 'high')->count();
        
        $statusDistribution = [
            'pending' => $pending,
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'completed' => $completed,
        ];
        
        return compact('total', 'completed', 'pending', 'highPriority', 'statusDistribution');
    }

    public function getAvailableUsers()
    {
        return User::all();
    }
}
