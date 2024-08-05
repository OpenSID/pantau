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
        return view('website.layanandesa.index', [
            'fillters' => $fillters,
            'total_versi' => 2,
            'versi_terakhir' => '2407.0.0'
        ]);
    }

    public function detail()
    {
        return view('website.layanandesa.detail');
    }
}
