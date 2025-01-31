<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PetaPeriodController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $period = $request->get('period') ?? Carbon::now()->format('Y-m-d').' - '.Carbon::now()->format('Y-m-d');
            $tanggalAwal = explode(' - ', $period)[0];
            $tanggalAkhir = explode(' - ', $period)[1];

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

            $geoJSONdata = Desa::fillter($fillters)->petaSemua()->whereBetween('created_at', [$tanggalAwal.' 00:00:00', $tanggalAkhir.' 23:59:59'])->get()->map(function ($desa) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            (float) $desa->lng,
                            (float) $desa->lat,
                        ],
                    ],
                    'properties' => $this->properties($desa),
                    'id' => $desa->id,
                ];
            });

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $geoJSONdata,
            ]);
        }

        return view('peta.index');
    }

    private function properties($desa)
    {
        $link = '';
        if (auth()->check()) {
            $link = '<tr><td>Website</td><td> : <a href="http://'.strtolower($desa->url_hosting).'" target="_blank">'.strtolower($desa->url_hosting).'</a></b></td></tr>';
        }

        return [
            'logo' => null,
            'popupContent' => '
                <h6 class="text-center"><b style="color:red">'.strtoupper($desa->sebutan_desa.' '.$desa->nama_desa).'</b></h6>
                <b><table width="100%">
                    <tr>
                        <td>'.ucwords($desa->sebutan_desa).'</td><td> : '.ucwords($desa->sebutan_desa.' '.$desa->nama_desa).'</b></td>
                    </tr>
                    <tr>
                        <td>Kecamatan</td><td> : '.ucwords($desa->nama_kecamatan).'</b></td>
                    </tr>
                    <tr>
                    <td>Kab/Kota</td><td> : '.ucwords($desa->nama_kabupaten).'</b></td>
                    </tr>
                    <tr>
                        <td>Provinsi</td><td> : '.ucwords($desa->nama_provinsi).'</b></td>
                    </tr>
                    <tr>
                        <td>Alamat</td><td> : '.$desa->alamat_kantor.'</b></td>
                    </tr>
                    '.$link.'
                </table></b>',
        ];
    }
}
