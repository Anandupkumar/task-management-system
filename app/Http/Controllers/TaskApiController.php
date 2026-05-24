<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskApiController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected TaskService $taskService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);
        $tasks = $this->taskService->getAllTasks($request->only(['status', 'priority']), $request->user());
        
        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->createTask($request->validated());
        
        return response()->json(new TaskResource($task), 201);
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task)
    {
        $task = $this->taskService->updateTaskStatus($task->id, $request->validated('status'));
        
        return response()->json(new TaskResource($task), 200);
    }

    public function aiSummary(Task $task)
    {
        $this->authorize('view', $task);
        $task = $this->taskService->getTaskForAISummary($task->id);
        
        return response()->json([
            'ai_summary'  => $task->ai_summary,
            'ai_priority' => $task->ai_priority?->value,
        ], 200);
    }
}
