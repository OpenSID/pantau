<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LayananDesaDashboardController extends Controller
{
    public function index(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];
        $versiTerakhir = lastrelease_api_layanandesa();
        return view('website.layanandesa.index', [
            'fillters' => $fillters,
            'total_versi' => 2,
            'versi_terakhir' => $versiTerakhir,
            'info_rilis' => 'Rilis LayananDesa '.$versiTerakhir
        ]);
    }

    public function detail()
    {
        return view('website.layanandesa.detail');
    }
}
