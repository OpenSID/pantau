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
                'kode_desa' => $desa->kode_desa,
                'nama_desa' => $desa->nama_desa,
                'kode_kecamatan' => $desa->kode_kecamatan,
                'nama_kecamatan' => $desa->nama_kecamatan,
                'kode_kabupaten' => $desa->kode_kabupaten,
                'nama_kabupaten' => $desa->nama_kabupaten,
                'kode_provinsi' => $desa->kode_provinsi,
                'nama_provinsi' => $desa->nama_provinsi,
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
