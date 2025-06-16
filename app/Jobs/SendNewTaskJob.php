<?php

namespace App\Jobs;

use App\Mail\NewTaskMail;
use App\Models\Task;
use App\Services\TaskNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct()
    {

    }

    public function handle(TaskNotificationService $taskNotificationService): void
    {
        try {
            $taskIds = $taskNotificationService->getNewTasks();

            Task::whereIn('id', $taskIds)
                ->with('user')
                ->chunk(100, function ($tasks) {
                    foreach ($tasks as $task) {
                        if ($task->user && $task->user->email) {
                             Mail::to($task->user->email)->send(new NewTaskMail($task));
                             Log::info("New task email sent to user ID: {$task->user->id} for task ID: {$task->id}");
                        }else{
                             Log::warning("User or email not found for task ID: {$task->id}");
                        }
                    }
                });

            // Mark tasks as sent
            $taskNotificationService->markAsSend($taskIds);

        } catch (\Exception $e) {
            Log::error("Failed to dispatch task emails", [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

}
