<?php

namespace App\Http\Controllers;

use App\Exports\DesaExport;
use App\Models\Desa;
use App\Models\Opendk;
use App\Models\Openkab;
use App\Models\Pbb;
use App\Models\PengaturanAplikasi;
use App\Models\TrackKeloladesa;
use App\Models\TrackMobile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    /** @var Desa */
    protected $desa;

    public function __construct()
    {
        $this->desa = new Desa();
    }

    public function index()
    {
        $pengaturanAplikasi = PengaturanAplikasi::get_pengaturan();
        $pengaturanAplikasi['akhir_backup'] = ! empty($pengaturanAplikasi['akhir_backup']) ? $pengaturanAplikasi['akhir_backup'] : Carbon::now()->startOfMonth()->format('Y-m-d');

        return view('dashboard', [
            'jumlahDesa' => $this->desa->jumlahDesa()->get()->first(),
            'desaBaru' => $this->desa->desaBaru()->count(),
            'kabupatenKosong' => collect($this->desa->kabupatenKosong())->count(),
            'info_backup' => [
                'cloud_storage' => $pengaturanAplikasi['cloud_storage'],
                'akhir_backup' => $pengaturanAplikasi['akhir_backup'],
                'waktu_backup' => $pengaturanAplikasi['waktu_backup'],
                'info' => 'Peringatan !!!',
                'isi' => 'Gagal Backup Otomatis ke Cloud Storage pada tanggal '.Carbon::createFromFormat('Y-m-d', $pengaturanAplikasi['akhir_backup'])->addDays($pengaturanAplikasi['waktu_backup'])->format('Y-m-d'),
            ],
        ]);
    }

    public function datatableDesaBaru(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of($this->desa->filterWilayah($request)->desaBaru()->get()->map(function ($desa) {
                if (auth()->check() == false) {
                    unset($desa['url_hosting']);
                }

                return $desa;
            }))->addIndexColumn()->make(true);
        }

        abort(404);
    }

    public function datatableSemuaDesa(Request $request)
    {
        if ($request->excel) {
            $paramDatatable = json_decode($request->get('params'), 1);
            $request->merge($paramDatatable);
        }

        $filters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
            'status' => $request->status,
            'akses' => $request->akses,
            'tte' => $request->tte,
            'versi_lokal' => '',
            'versi_hosting' => '',
        ];

        if ($request->ajax() || $request->excel) {
            $query = DataTables::of($this->desa->fillter($filters)->semuaDesa());

            if ($request->excel) {
                $query->filtering();

                return Excel::download(new DesaExport($query->results()), 'Desa-yang-memasang-OpenSID.xlsx');
            }

            return $query
                ->addIndexColumn()
                ->make(true);
        }

        abort(404);
    }

    public function datatableKabupatenKosong(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of($this->desa->kabupatenKosong())->addIndexColumn()->make(true);
        }

        abort(404);
    }

    public function datatableOpendkBaru(Request $request)
    {
        if ($request->ajax()) {
            $desa = Opendk::select('nama_kecamatan as region', 'created_at as tanggal')->orderBY('created_at', 'desc')->limit(7)->get()
                ->map(function ($item) {
                    $item->tanggal = formatDateTimeForHuman($item->tanggal); // Misalnya formatDateTimeForHuman merupakan fungsi untuk mengubah format tanggal
                    $item->tanggal = '<span class="text-nowrap text-muted">'.$item->tanggal.'</span>'; // Menambahkan kelas Bootstrap

                    return $item;
                });

            return DataTables::of($desa)
                ->addIndexColumn() // Menambahkan kolom indeks
                ->escapeColumns([])
                ->make(true);
        }

        abort(404); // Mengembalikan 404 jika bukan permintaan AJAX
    }

    public function datatableOpenkabBaru(Request $request)
    {
        if ($request->ajax()) {
            $desa = Openkab::select('nama_kab as region', 'created_at as tanggal')->orderBY('created_at', 'desc')->limit(7)->get()
                ->map(function ($item) {
                    $item->tanggal = formatDateTimeForHuman($item->tanggal); // Misalnya formatDateTimeForHuman merupakan fungsi untuk mengubah format tanggal
                    $item->tanggal = '<span class="text-nowrap text-muted">'.$item->tanggal.'</span>'; // Menambahkan kelas Bootstrap

                    return $item;
                });

            return DataTables::of($desa)
                ->addIndexColumn() // Menambahkan kolom indeks
                ->escapeColumns([])
                ->make(true);
        }

        abort(404); // Mengembalikan 404 jika bukan permintaan AJAX
    }

    public function datatableOpensidBaru(Request $request)
    {
        if ($request->ajax()) {
            $desa = Desa::select('nama_desa as region', 'created_at as tanggal')->orderBY('created_at', 'desc')->limit(7)->get()
                ->map(function ($item) {
                    $item->tanggal = formatDateTimeForHuman($item->tanggal); // Misalnya formatDateTimeForHuman merupakan fungsi untuk mengubah format tanggal
                    $item->tanggal = '<span class="text-nowrap text-muted">'.$item->tanggal.'</span>'; // Menambahkan kelas Bootstrap

                    return $item;
                });

            return DataTables::of($desa)
                ->addIndexColumn() // Menambahkan kolom indeks
                ->escapeColumns([])
                ->make(true);
        }

        abort(404); // Mengembalikan 404 jika bukan permintaan AJAX
    }

    public function datatableLayanandesaBaru(Request $request)
    {
        if ($request->ajax()) {
            $desa = TrackMobile::leftJoin('kode_wilayah', 'track_mobile.kode_desa', '=', 'kode_wilayah.kode_desa')
                ->select('track_mobile.created_at as tanggal', 'kode_wilayah.nama_desa as region')
                ->orderBy('track_mobile.created_at', 'desc')
                ->limit(7)
                ->get()
                ->map(function ($item) {
                    $item->tanggal = formatDateTimeForHuman($item->tanggal); // Misalnya formatDateTimeForHuman merupakan fungsi untuk mengubah format tanggal
                    $item->tanggal = '<span class="text-nowrap text-muted">'.$item->tanggal.'</span>'; // Menambahkan kelas Bootstrap

                    return $item;
                });

            return DataTables::of($desa)
                ->addIndexColumn() // Menambahkan kolom indeks
                ->escapeColumns([])
                ->make(true);
        }

        abort(404); // Mengembalikan 404 jika bukan permintaan AJAX
    }

    public function datatableKeloladesaBaru(Request $request)
    {
        if ($request->ajax()) {
            $desa = TrackKeloladesa::leftJoin('kode_wilayah', 'track_keloladesa.kode_desa', '=', 'kode_wilayah.kode_desa')
                ->select('track_keloladesa.created_at as tanggal', 'kode_wilayah.nama_desa as region')
                ->orderBy('track_keloladesa.created_at', 'desc')
                ->limit(7)
                ->get()
                ->map(function ($item) {
                    $item->tanggal = formatDateTimeForHuman($item->tanggal); // Misalnya formatDateTimeForHuman merupakan fungsi untuk mengubah format tanggal
                    $item->tanggal = '<span class="text-nowrap text-muted">'.$item->tanggal.'</span>'; // Menambahkan kelas Bootstrap

                    return $item;
                });

            return DataTables::of($desa)
                ->addIndexColumn() // Menambahkan kolom indeks
                ->escapeColumns([])
                ->make(true);
        }

        abort(404); // Mengembalikan 404 jika bukan permintaan AJAX
    }

    public function datatablePbbBaru(Request $request)
    {
        if ($request->ajax()) {
            $desa = Pbb::select('nama_desa as region', 'created_at as tanggal')->orderBY('created_at', 'desc')->limit(7)->get()
                ->map(function ($item) {
                    $item->tanggal = formatDateTimeForHuman($item->tanggal); // Misalnya formatDateTimeForHuman merupakan fungsi untuk mengubah format tanggal
                    $item->tanggal = '<span class="text-nowrap text-muted">'.$item->tanggal.'</span>'; // Menambahkan kelas Bootstrap

                    return $item;
                });

            return DataTables::of($desa)
                ->addIndexColumn() // Menambahkan kolom indeks
                ->escapeColumns([])
                ->make(true);
        }

        abort(404); // Mengembalikan 404 jika bukan permintaan AJAX
    }

    public function datatablePenggunaLayanandesa(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];

        if ($request->ajax()) {
            $desa = TrackMobile::filter($fillters)->leftJoin('kode_wilayah', 'track_mobile.kode_desa', '=', 'kode_wilayah.kode_desa')
                ->orderBy('track_mobile.created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->tanggal = formatDateTimeForHuman($item->created_at); // Misalnya formatDateTimeForHuman merupakan fungsi untuk mengubah format tanggal
                    $item->tanggal = '<span class="text-nowrap text-muted">'.$item->tanggal.'</span>'; // Menambahkan kelas Bootstrap

                    return $item;
                });

            return DataTables::of($desa)
                ->addIndexColumn() // Menambahkan kolom indeks
                ->escapeColumns([])
                ->make(true);
        }

        abort(404); // Mengembalikan 404 jika bukan permintaan AJAX
    }

    public function datatablePenggunaOpendk(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];

        if ($request->ajax()) {
            $desa = Opendk::filter($fillters)->orderBY('created_at', 'desc')->get()
                ->map(function ($item) {
                    $item->tanggal = formatDateTimeForHuman($item->created_at); // Misalnya formatDateTimeForHuman merupakan fungsi untuk mengubah format tanggal
                    $item->tanggal = '<span class="text-nowrap text-muted">'.$item->tanggal.'</span>'; // Menambahkan kelas Bootstrap

                    return $item;
                });

            return DataTables::of($desa)
                ->editColumn('updated_at', static fn ($q) => $q->updated_at->format('Y-m-d H:i:s'))
                ->addIndexColumn() // Menambahkan kolom indeks
                ->escapeColumns([])
                ->make(true);
        }

        abort(404); // Mengembalikan 404 jika bukan permintaan AJAX
    }

    public function datatablePenggunaPbb(Request $request)
    {
        if ($request->ajax()) {
            $desa = Pbb::orderBY('created_at', 'desc')->get()
                ->map(function ($item) {
                    $item->tanggal = formatDateTimeForHuman($item->created_at); // Misalnya formatDateTimeForHuman merupakan fungsi untuk mengubah format tanggal
                    $item->tanggal = '<span class="text-nowrap text-muted">'.$item->tanggal.'</span>'; // Menambahkan kelas Bootstrap

                    return $item;
                });

            return DataTables::of($desa)
                ->addIndexColumn() // Menambahkan kolom indeks
                ->escapeColumns([])
                ->make(true);
        }

        abort(404); // Mengembalikan 404 jika bukan permintaan AJAX
    }

    public function datatablePenggunaKeloladesa(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];

        if ($request->ajax()) {
            $desa = TrackKeloladesa::filter($fillters)->leftJoin('kode_wilayah', 'track_keloladesa.kode_desa', '=', 'kode_wilayah.kode_desa')
                ->orderBy('track_keloladesa.created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    $item->tanggal = formatDateTimeForHuman($item->created_at); // Misalnya formatDateTimeForHuman merupakan fungsi untuk mengubah format tanggal
                    $item->tanggal = '<span class="text-nowrap text-muted">'.$item->tanggal.'</span>'; // Menambahkan kelas Bootstrap

                    return $item;
                });

            return DataTables::of($desa)
                ->addIndexColumn() // Menambahkan kolom indeks
                ->escapeColumns([])
                ->make(true);
        }

        abort(404); // Mengembalikan 404 jika bukan permintaan AJAX
    }

    public function dataPeta(Request $request)
    {
        $markers = Desa::select(['lat', 'lng', 'alamat_kantor'])
            ->when($request->period ?? false, function ($subQuery) use ($request) {
                $dates = explode(' - ', $request->period);
                if (count($dates) === 2) {
                    // Validasi jika tanggal awal dan akhir berbeda
                    if ($dates[0] !== $dates[1]) {
                        $subQuery->whereBetween('created_at', [$dates[0], $dates[1]]);
                    } else {
                        $subQuery->whereDate('created_at', '=', $dates[0]);
                    }
                }
            })
            ->whereNotNullLatLng()
            ->get()
            ->map(function ($marker) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            (float) $marker->lng,
                            (float) $marker->lat,
                        ],
                    ],
                    'properties' => $this->properties($marker),
                    'id' => $marker->id,
                ];
            });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $markers,
        ]);
    }

    public function properties(Desa $desa)
    {
        $alamat = $desa->alamat_kantor;

        return [
            'popupContent' => "{$alamat}",
        ];
    }

    public function datatablePenggunaOpenkab(Request $request)
    {
        if ($request->ajax()) {
            $desa = Openkab::orderBY('created_at', 'desc')->get()
                ->map(function ($item) {
                    $item->tanggal = formatDateTimeForHuman($item->created_at); // Misalnya formatDateTimeForHuman merupakan fungsi untuk mengubah format tanggal
                    $item->tanggal = '<span class="text-nowrap text-muted">'.$item->tanggal.'</span>'; // Menambahkan kelas Bootstrap

                    return $item;
                });

            return DataTables::of($desa)
                ->addIndexColumn() // Menambahkan kolom indeks
                ->escapeColumns([])
                ->make(true);
        }

        abort(404); // Mengembalikan 404 jika bukan permintaan AJAX
    }

    public function datatablePenggunaOpensid(Request $request)
    {
        $filters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
            'status' => $request->status,
            'akses' => $request->akses,
            'tte' => $request->tte,
            'versi_lokal' => '',
            'versi_hosting' => '',
        ];

        if ($request->ajax()) {
            return DataTables::of($this->desa->fillter($filters)->semuaDesa()->orderBy('created_at', 'desc'))
                ->editColumn('modul_tte', function ($item) {
                    if ($item->modul_tte == 0) {
                        return '<span class="badge badge-secondary">Tidak Aktif</span>';
                    } elseif ($item->modul_tte == 1) {
                        return '<span class="badge badge-success">Aktif</span>';
                    }
                })->editColumn('tanggal', static fn ($item) => '<span class="text-nowrap text-muted">'.formatDateTimeForHuman($item->created_at).'</span>')
                ->addIndexColumn() // Menambahkan kolom indeks
                ->escapeColumns()
                ->rawColumns(['modul_tte', 'tanggal'])
                ->make(true);
        }

        abort(404); // Mengembalikan 404 jika bukan permintaan AJAX
    }
}
