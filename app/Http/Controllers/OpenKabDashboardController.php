<?php

namespace App\Http\Controllers;

use App\Models\Openkab;
use Illuminate\Http\Request;

class OpenKabDashboardController extends Controller
{
    public function peta(Request $request)
    {
        if ($request->ajax()) {
            $geoJSONdata = Openkab::with(['desa' => function ($query) {
                $query
                    ->whereRaw("CONCAT('',lat * 1) = lat") // tdk ikut sertakan data bukan bilangan
                    ->whereRaw("CONCAT('',lng * 1) = lng") // tdk ikut sertakan data bukan bilangan
                    ->whereRaw('lat BETWEEN -10 AND 6')
                    ->whereRaw('lng BETWEEN 95 AND 142')                
                    ->where(function ($query) {
                        $query
                            ->where('lat', '!=', config('tracksid.desa_contoh.lat'))
                            ->where('lng', '!=', config('tracksid.desa_contoh.lng'));
                    })                
                    ->orderBy('kode_desa', 'ASC');
            }])
            ->get()
            ->map(function ($data) {
                $desa = $data->desa
                    ?->filter(function ($item) {
                        return null !== $item->lat && null !== $item->lng;
                    })
                    ->first();

                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            (float) $desa?->lng,
                            (float) $desa?->lat,
                        ],
                    ],
                    'properties' => $this->properties($desa),
                    'id'         => $desa?->id,
                ];
            });

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $geoJSONdata,
            ]);
        }
    }

    private function properties($desa)
    {
        return [
            'logo' => null,
            'popupContent' => '
                <h6 class="text-center"><b style="color:red">'.strtoupper($desa?->nama_kabupaten).'</b></h6>
                <b>
                    <table width="100%">
                        <td>Kab/Kota</td><td> : '.ucwords($desa?->nama_kabupaten).'</b></td>
                        </tr>
                        <tr>
                            <td>Provinsi</td><td> : '.ucwords($desa?->nama_provinsi).'</b></td>
                        </tr>
                    </table>
                </b>',
        ];
    }
}