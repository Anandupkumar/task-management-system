<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\AIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateTaskAISummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly int $taskId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AIService $aiService): void
    {
        $task = Task::find($this->taskId);
        
        if (!$task) {
            Log::warning("GenerateTaskAISummaryJob: Task {$this->taskId} not found.");
            return;
        }

        $result = $aiService->generateSummary($task);

        $task->update([
            'ai_summary'  => $result['ai_summary'],
            'ai_priority' => $result['ai_priority'],
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("AI summary job failed for task {$this->taskId}: " . $exception->getMessage());
    }
}
