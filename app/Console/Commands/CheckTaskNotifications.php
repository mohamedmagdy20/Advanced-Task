<?php

namespace App\Console\Commands;

use App\Jobs\SendNewTaskJob;
use App\Services\TaskNotificationService;
use Illuminate\Console\Command;

class CheckTaskNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:check-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(TaskNotificationService $service)
    {
        SendNewTaskJob::dispatch($service);
    }
}
