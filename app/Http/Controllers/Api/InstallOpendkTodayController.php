<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Opendk;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InstallOpendkTodayController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {                
        $installHariIni = Opendk::filterWilayah($request)->whereDate('created_at', '>=', Carbon::now()->format('Y-m-d'))->get()->map(function($item) {
            return [
                'id' => $item->id,
                'versi' => $item->versi,
                'kode_kecamatan' => $item->kode_kecamatan,
                'nama_kecamatan' => $item->nama_kecamatan,
                'nama_kabupaten' => $item->nama_kabupaten,
                'nama_provinsi' => $item->nama_provinsi,
                'created_at' => $item->created_at,
                'created_at_format_human' => formatDateTimeForHuman($item->created_at),
            ];
        });

        return [     
            'installHariIni' => $installHariIni,
        ];
    }
}
