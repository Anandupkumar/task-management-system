<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Generate AI summary and priority based on task.
     *
     * @param Task $task
     * @return array
     */
    public function generateSummary(Task $task): array
    {
        if (!config('services.ai.key')) {
            return $this->mockResponse($task);
        }

        try {
            $response = Http::timeout(15)
                ->retry(2, 1000)
                ->withToken(config('services.ai.key'))
                ->post(config('services.ai.url'), [
                    'prompt' => $this->buildPrompt($task),
                ]);

            if ($response->successful()) {
                return $this->parseResponse($response->json());
            }

            Log::warning('AIService API failed, falling back to mock. Status: ' . $response->status());
            return $this->mockResponse($task);
        } catch (\Throwable $e) {
            Log::warning('AIService exception triggered, falling back to mock: ' . $e->getMessage());
            return $this->mockResponse($task);
        }
    }

    private function buildPrompt(Task $task): string
    {
        return <<<PROMPT
        You are a task analysis assistant. Analyze the task and respond ONLY with valid JSON.

        Task Title: {$task->title}
        Description: {$task->description}
        Priority: {$task->priority->value}
        Due Date: {$task->due_date?->format('Y-m-d')}

        Respond with:
        {
          "ai_summary": "A concise 1-2 sentence summary",
          "ai_priority": "low|medium|high"
        }
        PROMPT;
    }

    private function parseResponse(array $data): array
    {
        return [
            'ai_summary'  => $data['ai_summary'] ?? 'Generated summary missing.',
            'ai_priority' => $data['ai_priority'] ?? 'medium',
        ];
    }

    private function mockResponse(Task $task): array
    {
        return [
            'ai_summary'  => "Task '{$task->title}' has been analyzed. Based on its description and due date, it requires structured attention.",
            'ai_priority' => $task->priority->value,
        ];
    }
}
