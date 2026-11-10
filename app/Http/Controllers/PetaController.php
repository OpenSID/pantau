<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Services\PetaDesaService;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    public function __construct()
    {
        config()->set('adminlte.sidebar_collapse', true);
        config()->set('adminlte.sidebar_collapse_remember', false);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
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

            $desa = Desa::fillter($fillters)->peta()->get();

            return response()->json(PetaDesaService::featureCollection($desa));
        }

        return view('peta.index');
    }
}
