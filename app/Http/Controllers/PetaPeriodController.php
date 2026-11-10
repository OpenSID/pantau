<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Services\PetaDesaService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PetaPeriodController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $period = $request->get('period') ?? Carbon::now()->format('Y-m-d').' - '.Carbon::now()->format('Y-m-d');
            $tanggalAwal = explode(' - ', $period)[0];
            $tanggalAkhir = explode(' - ', $period)[1];

            $fillters = [
                'kode_provinsi' => $request->kode_provinsi,
                'kode_kabupaten' => $request->kode_kabupaten,
                'kode_kecamatan' => $request->kode_kecamatan,
                'status' => $request->status,
                'akses' => $request->akses,
                'versi_lokal' => $request->versi_lokal,
                'versi_hosting' => $request->versi_hosting,
                'tte' => $request->tte,
            ];

            $desa = Desa::fillter($fillters)
                ->petaSemua()
                ->whereBetween('created_at', [$tanggalAwal.' 00:00:00', $tanggalAkhir.' 23:59:59'])
                ->get();

            return response()->json(PetaDesaService::featureCollection($desa));
        }

        return view('peta.index');
    }
}
