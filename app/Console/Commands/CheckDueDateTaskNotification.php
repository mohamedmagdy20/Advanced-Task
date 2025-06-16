<?php

namespace App\Console\Commands;

use App\Jobs\SendTaskReminderJob;
use Illuminate\Console\Command;

class CheckDueDateTaskNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:check-due-date-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SendTaskReminderJob::dispatch();
    }
}
