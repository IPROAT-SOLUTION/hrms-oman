<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule)
    {
        $month = date('Y-m');
        $schedule->command('report')->hourly()->runInBackground();
        $schedule->command('annual-leave')->dailyAt('01:00')->runInBackground();
        // $schedule->command('database:backup')->runInBackground();
        $schedule->command("generate:weeklyholiday {$month}")->monthly()->runInBackground();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
