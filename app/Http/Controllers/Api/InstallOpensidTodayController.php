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
        // Definisi online/offline sama dengan laporan lain (lihat Desa::sqlOnline/sqlOffline)
        $totalInstallOnline = Desa::filterWilayah($request)->hostingOnline()->count();
        $totalInstallOffline = Desa::filterWilayah($request)->hostingOffline()->count();
        $installHariIni = Desa::filterWilayah($request)->whereDate('created_at', '>=', Carbon::now()->format('Y-m-d'))->get();

        return [
            'total' => ['online' => $totalInstallOnline, 'offline' => $totalInstallOffline],
            'installHariIni' => $installHariIni,
        ];
    }
}
