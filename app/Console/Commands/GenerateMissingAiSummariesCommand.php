<?php

namespace App\Console\Commands;

use App\Jobs\GenerateTaskAISummaryJob;
use App\Models\Task;
use Illuminate\Console\Command;

class GenerateMissingAiSummariesCommand extends Command
{
    protected $signature = 'tasks:generate-ai-summaries';

    protected $description = 'Queue AI summary jobs for tasks missing ai_summary';

    public function handle(): int
    {
        $tasks = Task::query()->whereNull('ai_summary')->get();

        if ($tasks->isEmpty()) {
            $this->info('No tasks need AI summaries.');

            return self::SUCCESS;
        }

        foreach ($tasks as $task) {
            GenerateTaskAISummaryJob::dispatch($task->id);
        }

        $this->info("Queued AI summary jobs for {$tasks->count()} task(s).");
        $this->line('Ensure a queue worker is running: php artisan queue:work');

        return self::SUCCESS;
    }
}
