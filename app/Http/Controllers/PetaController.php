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

        $geoJSONdata = Desa::latest()->peta($fillters)->get()->map(function ($desa) {
            return [
                'type'       => 'Feature',
                'geometry'   => [
                    'type'        => 'Point',
                    'coordinates' => [
                        (float) $desa->lng,
                        (float) $desa->lat
                    ],
                ],
                'properties' => $this->properties($desa),
                'id' => $desa->id,
            ];
        });

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $geoJSONdata,
        ]);
    }

    private function properties($desa)
    {
        return [
            'logo'   => null,
            'popupContent' => '
                <h6 class="text-center"><b style="color:red">' . strtoupper($desa->sebutan_desa . ' ' . $desa->nama_desa) . '</b></h6>
                <b><table width="100%">
                    <tr>
                        <td>Desa</td><td> : ' . $desa->sebutan_desa . ' ' . $desa->nama_desa . '</b></td>
                    </tr>
                    <tr>
                        <td>Kecamatan</td><td> : ' . $desa->nama_kecamatan . '</b></td>
                    </tr>
                    <tr>
                    <td>Kab/Kota</td><td> : ' . $desa->nama_kabupaten . '</b></td>
                    </tr>
                    <tr>
                        <td>Provinsi</td><td> : ' . $desa->nama_provinsi . '</b></td>
                    </tr>
                    <tr>
                        <td>Alamat</td><td> : ' . $desa->alamat_kantor . '</b></td>
                    </tr>
                    <tr>
                        <td>Website</td><td> : <a href="' . $desa->url_hosting . ' target="_blank">' . $desa->url_hosting . '</a></b></td>
                    </tr>
                </table></b>',
        ];
    }
}
