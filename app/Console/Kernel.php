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
        $schedule->command('tracksid:optimasi-desa')->weekly(); // setiap minggu at 00:00
        $schedule->command('tracksid:backup-database-storage')->weekly()->sundays()->timezone('Asia/Jakarta')->at('01:00'); // setiap minggu at 01:00
        $schedule->command('tracksid:backup-google-drive')->weekly()->sundays()->timezone('Asia/Jakarta')->at('03:00'); // setiap minggu at 03:00
        $schedule->command('tracksid:backup-vps-sftp')->weekly()->sundays()->timezone('Asia/Jakarta')->at('03:30'); // setiap minggu at 03:00
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
