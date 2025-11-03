<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\Opendk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OpenDKDashboardController extends Controller
{
    public function index(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,            
        ];
        $versiTerakhir = lastrelease_opendk();
        $installHariIni = Opendk::whereDate('created_at', '>=', Carbon::now()->format('Y-m-d'))->get();

        return view('website.opendk.index', [
            'fillters' => $fillters,
            'info_rilis' => 'Rilis OpenDK '.$versiTerakhir,
            'installHariIni' => $installHariIni,
            'provinsi_pengguna_opendk' => Opendk::selectRaw('nama_provinsi, count(*) as total')->orderBy('total', 'desc')->groupBy('nama_provinsi')->get(),
        ]);
    }

    public function detail(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
            'akses' => $request->akses,
        ];

        return view('website.opendk.detail', compact('fillters'));
    }

    public function versi(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];

        if ($request->ajax()) {
            return DataTables::of(Opendk::filter($fillters)->groupBy('versi')->selectRaw('versi, count(*) as jumlah'))
                ->addIndexColumn()
                ->make(true);
        }

        return view('website.opendk.versi_lengkap', compact('fillters'));
    }

    public function versi_detail(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];

        if ($request->ajax()) {
            $versi = $request->versi;

            return DataTables::of(Opendk::filter($fillters)->when($versi, static fn ($q) => $q->where('versi', $versi)))
                ->editColumn('updated_at', static fn ($q) => $q->updated_at->translatedFormat('Y-m-d H:i:s'))
                ->addIndexColumn()
                ->make(true);
        }

        return view('website.opendk.versi_detail', compact('fillters'));
    }

    public function install_baru(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Opendk::filterWilayah($request)->when($request->period ?? false, function ($subQuery) use ($request) {
                $dates = explode(' - ', $request->period);
                if (count($dates) === 2) {
                    // Validasi jika tanggal awal dan akhir berbeda
                    if ($dates[0] !== $dates[1]) {
                        $subQuery->whereBetween('created_at', [$dates[0], $dates[1]]);
                    } else {
                        $subQuery->whereDate('created_at', '=', $dates[0]);
                    }
                }
            }, function ($subQuery) {
                // Jika $request->period kosong, gunakan filter default
                $subQuery->whereDate('created_at', '>=', Carbon::now()->subDays(7));
            }))
                ->editColumn('created_at', static fn ($q) => $q->created_at->translatedFormat('j F Y H:i'))
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function peta(Request $request)
    {
        if ($request->ajax()) {
            $fillters = [
                'kode_provinsi' => $request->kode_provinsi,
                'kode_kabupaten' => $request->kode_kabupaten,
                'kode_kecamatan' => $request->kode_kecamatan,
                'period' => $request->period,
            ];
            //
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
                        'id' => $kecamatan->id,
                    ];
                });

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $geoJSONdata,
            ]);
        }
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
