<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('pantau:optimasi-desa')->weekly(); // setiap minggu at 00:00
Schedule::command('pantau:sinkronasi-bps-kemendagri')->weekly(); // setiap minggu at 00:00
Schedule::command('pantau:backup-database-storage')->timezone('Asia/Jakarta')->at('01:00'); // setiap hari at 01:00
Schedule::command('pantau:backup-google-drive')->timezone('Asia/Jakarta')->at('03:00'); // setiap hari at 03:00
Schedule::command('pantau:backup-vps-sftp')->timezone('Asia/Jakarta')->at('03:30'); // setiap hari at 03:00
