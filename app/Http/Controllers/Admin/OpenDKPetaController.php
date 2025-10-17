<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opendk;
use Illuminate\Http\Request;

class OpenDKPetaController extends Controller
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
                'period' => $request->period,
            ];

            $geoJSONdata = Opendk::filter($fillters)
                ->whereRaw("CONCAT('',lat * 1) = lat") // tdk ikut sertakan data bukan bilangan
                ->whereRaw("CONCAT('',lng * 1) = lng") // tdk ikut sertakan data bukan bilangan
                ->whereRaw('lat BETWEEN -10 AND 6')
                ->whereRaw('lng BETWEEN 95 AND 142')
                ->where(function ($query) {
                    $query
                        ->where('lat', '!=', config('tracksid.desa_contoh.lat'))
                        ->where('lng', '!=', config('tracksid.desa_contoh.lng'));
                })->orderBy('kode_kecamatan', 'ASC')->get()->map(function ($kecamatan) {
                    return [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'Point',
                            'coordinates' => [
                                (float) $kecamatan->lng,
                                (float) $kecamatan->lat,
                            ],
                        ],
                        'properties' => $this->properties($kecamatan),
                        'id' => $kecamatan->kode_kecamatan,
                    ];
                });

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $geoJSONdata,
            ]);
        }

        return view('admin.opendk.peta.index');
    }

    private function properties(Opendk $kecamatan)
    {
        $link = '';
        if (auth()->check()) {
            $link = '<tr><td>Website</td><td> : <a href="http://'.strtolower($kecamatan->url).'" target="_blank">'.strtolower($kecamatan->url).'</a></b></td></tr>';
        }

        return [
            'logo' => null,
            'popupContent' => '
                <h6 class="text-center"><b style="color:red">'.strtoupper($kecamatan->sebutan_wilayah.' '.$kecamatan->nama_kecamatan).'</b></h6>
                <b><table width="100%">
                    <tr>
                        <td>'.ucwords($kecamatan->sebutan_wilayah).'</td><td> : '.ucwords($kecamatan->sebutan_wilayah.' '.$kecamatan->nama_kecamatan).'</b></td>
                    </tr>
                    <tr>
                        <td>Kecamatan</td><td> : '.ucwords($kecamatan->nama_kecamatan).'</b></td>
                    </tr>
                    <tr>
                    <td>Kab/Kota</td><td> : '.ucwords($kecamatan->nama_kabupaten).'</b></td>
                    </tr>
                    <tr>
                        <td>Provinsi</td><td> : '.ucwords($kecamatan->nama_provinsi).'</b></td>
                    </tr>
                    <tr>
                        <td>Alamat</td><td> : '.$kecamatan->alamat.'</b></td>
                    </tr>
                    '.$link.'
                </table></b>',
        ];
    }
}
