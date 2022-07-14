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

    public function desa()
    {
        // $fillters = [
        //     'kode_provinsi'  => request('kode_provinsi'),
        //     'kode_kabupaten' => request('kode_kabupaten'),
        //     'kode_kecamatan' => request('kode_kecamatan'),
        //     'status' => request('status'),
        // ];

        $fillters = [
            'kode_provinsi'  => '35', // request('kode_provinsi'),
            'kode_kabupaten' => '35.22', // request('kode_kabupaten'),
            'kode_kecamatan' => '35.22.15', // request('kode_kecamatan'),
            // 'status' => '1',
        ];


        $desa = Desa::latest()->peta($fillters)->get();

        $geoJSONdata = $desa->map(function ($desa) {
            return [
                'sebutan_desa' => $desa->sebutan_desa,
                'desa'         => $desa->nama_desa,
                'kecamatan'    => $desa->nama_kecamatan,
                'kabupaten'    => $desa->nama_kabupaten,
                'provinsi'     => $desa->nama_provinsi,
                'web'          => $desa->url_hosting,
                'alamat'       => $desa->alamat_kantor,
                'koordinat'    => [$desa->lat, $desa->lng],
                // 'kode_prov'    => $desa->kode_provinsi,
                // 'kode_kabupaten'    => $desa->kode_kabupaten,
                // 'kode_kecamatan'    => $desa->kode_kecamatan,
                // 'kode_desa'    => $desa->kode_desa,
            ];
        });

        return response()->json($geoJSONdata);
    }
}
