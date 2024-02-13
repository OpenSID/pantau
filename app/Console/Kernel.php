<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('tracksid:optimasi-desa')->weekly(); // setiap minggu at 00:00
        $schedule->command('tracksid:sinkronasi-bps-kemendagri')->weekly(); // setiap minggu at 00:00
        $schedule->command('tracksid:backup-database-storage')->timezone('Asia/Jakarta')->at('01:00'); // setiap hari at 01:00
        $schedule->command('tracksid:backup-google-drive')->timezone('Asia/Jakarta')->at('03:00'); // setiap hari at 03:00
        $schedule->command('tracksid:backup-vps-sftp')->timezone('Asia/Jakarta')->at('03:30'); // setiap hari at 03:00
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
