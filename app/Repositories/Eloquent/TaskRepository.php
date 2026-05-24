<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;

class TaskRepository implements TaskRepositoryInterface
{
    public function all(array $filters = [], ?User $user = null)
    {
        return Task::query()
            ->with('assignedUser')
            ->filter($filters)
            ->when(
                $user && !$user->isAdmin(),
                fn($q) => $q->where('assigned_to', $user->id)
            )
            ->paginate(10);
    }

    public function find(int $id): Task
    {
        return Task::with('assignedUser')->findOrFail($id);
    }

    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(int $id, array $data): Task
    {
        $task = $this->find($id);
        $task->update($data);
        
        return $task->refresh();
    }

    public function delete(int $id): void
    {
        $this->find($id)->delete();
    }
}
