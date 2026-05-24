<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected TaskService $taskService
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);
        $tasks = $this->taskService->getAllTasks($request->only(['status', 'priority']), $request->user());

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $this->authorize('create', Task::class);
        $users = $this->taskService->getAvailableUsers();

        return view('tasks.create', compact('users'));
    }

    public function store(StoreTaskRequest $request)
    {
        // authorization handled by form request
        $this->taskService->createTask($request->validated());

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $users = $this->taskService->getAvailableUsers();

        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        // authorization handled by form request
        $this->taskService->updateTask($task->id, $request->validated());

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $this->taskService->deleteTask($task->id);

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
