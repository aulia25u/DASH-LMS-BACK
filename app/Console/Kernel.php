<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call('sinau:getcategory')->dailyAt('00:00')->withoutOverlapping();
        $schedule->call('sinau:getcourse')->dailyAt('00:30')->withoutOverlapping();
        $schedule->call('sinau:getquiz')->dailyAt('01:30')->withoutOverlapping();
        $schedule->call('sinau:getquizattempts')->dailyAt('02:30')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
