<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class CommandController extends Controller
{
    /** perintah untuk notifikasi var_damp dan log laravel*/
    public function notifMessage($notif)
    {
        var_dump($notif);
        Log::channel('single')->info($notif);
    }
}
