<?php

namespace App\Http\Controllers;

use App\Models\Desa;

class PetaController extends Controller
{
    public function index()
    {
        return view('peta.index');
    }

    public function desa()
    {
        $desa = Desa::peta()->get();

        $geoJSONdata = $desa->map(function ($desa) {
            return [
                'sebutan_desa' => $desa->sebutan_desa ?? 'Desa',
                'desa' => $desa->nama_desa,
                'kecamatan' => $desa->nama_kecamatan,
                'kabupaten' => $desa->nama_kabupaten,
                'provinsi' => $desa->nama_provinsi,
                'web'           => $desa->url_hosting,
                'versi'         => $desa->opensid_valid,
                'logo' => null,
                'tipe' => 'online',
                'koordinat' => [$desa->lat, $desa->lng],
            ];
        });

        return response()->json($geoJSONdata);
    }
}
