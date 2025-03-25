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
        //$schedule->command('app:send-overdue-notifications')->dailyAt('08:00');
      //$schedule->command('app:send-overdue-notifications')->everyTenMinutes();
	   	$schedule->command('app:send-overdue-notifications')->everyMinute();

//        $schedule->call(function () {
//            \App\Helpers\QuickBooksHelper::refreshToken();
//        })->hourly()->name('refresh-quickbooks-token');
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
