<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Check SIP expiry every day at 8 AM
        $schedule->command('sip:check-expiry')->dailyAt('08:00');

        // Sync attendance from machine every 30 minutes
        // $schedule->command('attendance:sync-machine')->everyThirtyMinutes();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
