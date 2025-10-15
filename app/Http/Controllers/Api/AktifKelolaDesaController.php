<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\TrackKeloladesa;
use Illuminate\Http\Request;

class AktifKelolaDesaController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $filters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];

        return [
            'aktif' => TrackKeloladesa::active()->filter($filters)->count() ?? 0,
            'desa_total' => Desa::desaValid()->filterWilayah($request)->count() ?? 0,
        ];
    }
}
