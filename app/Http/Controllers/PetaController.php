<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    public function index()
    {
        return view('peta.index');
    }

    public function desa(Request $request)
    {
        $fillters = [
            'kode_provinsi'  => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
            'status'         => $request->status,
        ];

        $desa = Desa::latest()->peta($fillters)->get()->map(function ($desa) {
            return [
                'sebutan_desa' => $desa->sebutan_desa,
                'desa'         => $desa->nama_desa,
                'kecamatan'    => $desa->nama_kecamatan,
                'kabupaten'    => $desa->nama_kabupaten,
                'provinsi'     => $desa->nama_provinsi,
                'web'          => $desa->url_hosting,
                'alamat'       => $desa->alamat_kantor,
                'koordinat'    => [$desa->lat, $desa->lng],
            ];
        });

        return response()->json($desa);
    }
}
