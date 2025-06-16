<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run Command every hour to Check upcoming Task Every Hour //
        $schedule->command('task:check-notifications')->hourly()->withoutOverlapping()
                 ->runInBackground();

        // // Run Command every hour to Check Deadline Task Every Before 24 hour every Hour //
        $schedule->command('task:check-due-date-notification')->hourly()->withoutOverlapping()
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
