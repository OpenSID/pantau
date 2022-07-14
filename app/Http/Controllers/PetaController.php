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
        $desa = Desa::peta()->get();

        $geoJSONdata = $desa->map(function ($desa) use ($request) {
            return [
                'sebutan_desa' => $desa->sebutan_desa,
                'desa'         => $desa->nama_desa,
                'kecamatan'    => $desa->nama_kecamatan,
                'kabupaten'    => $desa->nama_kabupaten,
                'provinsi'     => $desa->nama_provinsi,
                'web'          => $desa->url_hosting,
                'alamat'       => $desa->alamat_kantor,
                'koordinat'    => [$desa->lat, $desa->lng],
                'kode_prov'    => $request->get('kode_prov'),
                'kode_kab'     => $request->get('kode_kab'),
                'kode_kec'     => $request->get('kode_kec'),
                'status'       => $request->get('status'),
            ];
        });

        return response()->json($geoJSONdata);
    }
}
