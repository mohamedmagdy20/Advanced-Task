<?php

namespace App\Jobs;

use App\Mail\TaskReminderMail;
use App\Models\Task;
use App\Services\TaskNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTaskReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

     protected $taskNotificationService;
    public function __construct(TaskNotificationService $taskNotificationService)
    {
        $this->taskNotificationService = $taskNotificationService;
    }


    public function handle(): void
    {
        try {
            $taskIds = $this->taskNotificationService->getOverDueTask();

             Task::whereIn('id', $taskIds)
                ->with('user')
                ->chunk(100, function ($tasks) {
                    foreach ($tasks as $task) {
                        if ($task) {
                             Mail::to($task->user->email)
                            ->send(new TaskReminderMail($task));

                            Log::info("Task reminder email sent for task ID: {$task->id}");
              
                        }else{
                             Log::warning("User or email not found for task ID: {$task->id}");
                        }
                    }
                });
          
        } catch (\Exception $e) {
            Log::error("Failed to send task reminder email", [
                'error' => $e->getMessage()
            ]);     
            throw $e;
        }
    }


}
