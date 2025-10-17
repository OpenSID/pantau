<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InstallOpensidTodayController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $totalInstall = Desa::filterWilayah($request)->count();
        $totalInstallOnline = Desa::filterWilayah($request)->online()->count();
        $installHariIni = Desa::filterWilayah($request)->whereDate('created_at', '>=', Carbon::now()->format('Y-m-d'))->get();

        return [
            'total' => ['online' => $totalInstallOnline, 'offline' => $totalInstall - $totalInstallOnline],
            'installHariIni' => $installHariIni,
        ];
    }
}
