<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\TrackKeloladesa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KelolaDesaDashboardController extends Controller
{
    public function index(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];
        $versiTerakhir = lastrelease_api_layanandesa();
        $installHariIni = TrackKeloladesa::with(['desa'])->whereDate('created_at', '>=', Carbon::now()->startOfYear()->format('Y-m-d'))->get();

        return view('website.keloladesa.index', [
            'fillters' => $fillters,
            'total_versi' => 2,
            'total_desa' => format_angka(Desa::count()),
            'pengguna_layanan_desa' => TrackKeloladesa::distinct('kode_desa')->count(),
            'versi_terakhir' => $versiTerakhir,
            'info_rilis' => 'Rilis KelolaDesa ' . $versiTerakhir,
            'total_versi' => TrackKeloladesa::distinct('versi')->count(),
            'pengguna_versi_terakhir' => TrackKeloladesa::where('versi', $versiTerakhir)->count(),
            'installHariIni' => $installHariIni,
        ]);
    }

    public function detail(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];

        return view('website.keloladesa.detail', compact('fillters'));
    }

    public function versi(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];

        if ($request->ajax()) {
            return DataTables::of(TrackKeloladesa::groupBy('versi')->selectRaw('versi, count(*) as jumlah'))
                ->addIndexColumn()
                ->make(true);
        }

        return view('website.keloladesa.versi_lengkap', compact('fillters'));
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

            return DataTables::of(TrackKeloladesa::filter($fillters)->when($versi, static fn($q) => $q->where('versi', $versi))->with(['desa'])->groupBy(['versi', 'kode_desa'])->selectRaw('kode_desa, versi, count(*) as jumlah'))
                ->addIndexColumn()
                ->make(true);
        }

        return view('website.keloladesa.versi_detail', compact('fillters'));
    }

    public function install_baru(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(TrackKeloladesa::with('desa')->filter($request))
                ->editColumn('updated_at', static fn($q) => $q->updated_at->translatedFormat('j F Y H:i'))
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function summary(Request $request)
    {
        $provinsi = $request->get('provinsi');
        $kabupaten = $request->get('kabupaten');
        $kecamatan = $request->get('kecamatan');

        $summary = Desa::selectRaw('count(distinct kode_desa) as desa, count(distinct kode_kecamatan) as kecamatan, count(distinct kode_kabupaten) as kabupaten, count(distinct kode_provinsi) as provinsi')
            ->whereIn('kode_desa', function ($q) use ($request) {
                $q->selectRaw('distinct kode_desa')
                    ->from('track_keloladesa');

                // Menambahkan filter created_at pada subquery
                if ($request->period) {
                    $dates = explode(' - ', $request->period);
                    if (count($dates) === 2) {
                        // Jika periode mencakup rentang tanggal
                        if ($dates[0] !== $dates[1]) {
                            $q->whereBetween('created_at', [$dates[0], $dates[1]]);
                        } else {
                            // Jika hanya satu tanggal
                            $q->whereDate('created_at', '=', $dates[0]);
                        }
                    }
                }
            });

        $summarySebelumnya = Desa::selectRaw('count(distinct kode_desa) as desa, count(distinct kode_kecamatan) as kecamatan, count(distinct kode_kabupaten) as kabupaten, count(distinct kode_provinsi) as provinsi')->whereIn('kode_desa', function ($q) use ($request) {
            $q->selectRaw('distinct kode_desa')->from('track_keloladesa');

            if ($request->period) {
                $dates = explode(' - ', $request->period);
                if (count($dates) === 2) {
                    // Kurangi satu bulan dari setiap tanggal
                    $startDate = Carbon::parse($dates[0])->subMonth()->format('Y-m-d');
                    $endDate = Carbon::parse($dates[1])->subMonth()->format('Y-m-d');

                    // Jika periode mencakup rentang tanggal
                    if ($dates[0] !== $dates[1]) {
                        $q->whereBetween('created_at', [$startDate, $endDate]);
                    } else {
                        // Jika hanya satu tanggal
                        $q->whereDate('created_at', '=', $startDate);
                    }
                }
            }
        });

        if ($provinsi) {
            $summary->where('kode_provinsi', $provinsi);
            $summarySebelumnya->where('kode_provinsi', $provinsi);
        }
        if ($kabupaten) {
            $summary->where('kode_kabupaten', $kabupaten);
            $summarySebelumnya->where('kode_kabupaten', $kabupaten);
        }
        if ($kecamatan) {
            $summary->where('kode_kecamatan', $kecamatan);
            $summarySebelumnya->where('kode_kecamatan', $kecamatan);
        }
        $summareResult = $summary->first();
        $summarySebelumnyaResult = $summarySebelumnya->first();

        return response()->json(
            [
                'total' => [
                    'provinsi' => ['total' => $summareResult->provinsi, 'pertumbuhan' => $summareResult->provinsi - $summarySebelumnyaResult->provinsi],
                    'kabupaten' => ['total' => $summareResult->kabupaten, 'pertumbuhan' => $summareResult->kabupaten - $summarySebelumnyaResult->kabupaten],
                    'kecamatan' => ['total' => $summareResult->kecamatan, 'pertumbuhan' => $summareResult->kecamatan - $summarySebelumnyaResult->kecamatan],
                    'desa' => ['total' => $summareResult->desa, 'pertumbuhan' => $summareResult->desa - $summarySebelumnyaResult->desa],
                ],
            ]
        );
    }

    public function peta(Request $request)
    {
        if ($request->ajax()) {
            $fillters = [
                'kode_provinsi' => $request->kode_provinsi,
                'kode_kabupaten' => $request->kode_kabupaten,
                'kode_kecamatan' => $request->kode_kecamatan,
                'status' => null,
                'period' => $request->period,
                'akses' => null,
                'versi_lokal' => null,
                'versi_hosting' => null,
                'tte' => null,
            ];

            $geoJSONdata = Desa::fillter($fillters)
                ->whereRaw("CONCAT('',lat * 1) = lat") // tdk ikut sertakan data bukan bilangan
                ->whereRaw("CONCAT('',lng * 1) = lng") // tdk ikut sertakan data bukan bilangan
                ->whereRaw('lat BETWEEN -10 AND 6')
                ->whereRaw('lng BETWEEN 95 AND 142')
                ->where(function ($query) {
                    $query
                        ->where('lat', '!=', config('tracksid.desa_contoh.lat'))
                        ->where('lng', '!=', config('tracksid.desa_contoh.lng'));
                })
                ->whereIn('kode_desa', function ($q) use ($request) {
                    $q->selectRaw('distinct kode_desa')->from('track_keloladesa');

                    if ($request->period) {
                        $dates = explode(' - ', $request->period);
                        if (count($dates) === 2) {
                            // Jika periode mencakup rentang tanggal
                            if ($dates[0] !== $dates[1]) {
                                $q->whereBetween('created_at', [$dates[0], $dates[1]]);
                            } else {
                                // Jika hanya satu tanggal
                                $q->whereDate('created_at', '=', $dates[0]);
                            }
                        }
                    }
                })->orderBy('kode_desa', 'ASC')->get()->map(function ($desa) {
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
    }

    private function properties($desa)
    {
        $link = '';
        if (auth()->check()) {
            $link = '<tr><td>Website</td><td> : <a href="http://' . strtolower($desa->url_hosting) . '" target="_blank">' . strtolower($desa->url_hosting) . '</a></b></td></tr>';
        }

        return [
            'logo' => null,
            'popupContent' => '
                <h6 class="text-center"><b style="color:red">' . strtoupper($desa->sebutan_desa . ' ' . $desa->nama_desa) . '</b></h6>
                <b><table width="100%">
                    <tr>
                        <td>' . ucwords($desa->sebutan_desa) . '</td><td> : ' . ucwords($desa->sebutan_desa . ' ' . $desa->nama_desa) . '</b></td>
                    </tr>
                    <tr>
                        <td>Kecamatan</td><td> : ' . ucwords($desa->nama_kecamatan) . '</b></td>
                    </tr>
                    <tr>
                    <td>Kab/Kota</td><td> : ' . ucwords($desa->nama_kabupaten) . '</b></td>
                    </tr>
                    <tr>
                        <td>Provinsi</td><td> : ' . ucwords($desa->nama_provinsi) . '</b></td>
                    </tr>
                    <tr>
                        <td>Alamat</td><td> : ' . $desa->alamat_kantor . '</b></td>
                    </tr>
                    ' . $link . '
                </table></b>',
        ];
    }
}
