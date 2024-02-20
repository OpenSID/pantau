<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\TrackKeloladesa;
use App\Models\TrackMobile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Yajra\DataTables\Facades\DataTables;

class MobileController extends Controller
{
    private $mobile;

    private $kelolaDesa;

    protected $baseRoute = 'mobile';

    protected $baseView = 'mobile';

    public function __construct()
    {
        $this->mobile = new TrackMobile();
        $this->kelolaDesa = new TrackKeloladesa();
        Config::set('title', $this->baseView.'');
    }

    public function index()
    {
        $totalPengguna = $this->kelolaDesa->wilayahKhusus()->count();
        $totalDesaPengguna = $this->kelolaDesa->wilayahKhusus()->desa()->count();
        $totalDesaPenggunaAktif = $this->kelolaDesa->wilayahKhusus()->desa()->active()->count();
        $totalPenggunaAktif = $this->kelolaDesa->wilayahKhusus()->count();

        $totalPenggunaKelolaDesa = $this->kelolaDesa->wilayahKhusus()->count();
        $totalDesaPenggunaKelolaDesa = $this->kelolaDesa->wilayahKhusus()->desa()->count();
        $totalDesaPenggunaAktifKelolaDesa = $this->kelolaDesa->wilayahKhusus()->desa()->active()->count();
        $totalPenggunaAktifKelolaDesa = $this->kelolaDesa->wilayahKhusus()->count();

        $desaWidgets = [
            'semua' => ['urlWidget' => (Auth::check() ? url($this->baseRoute.'/pengguna') : ''), 'titleWidget' => 'Total Pengguna', 'classWidget' => 'col-lg-3', 'classBackgroundWidget' => 'bg-info', 'totalWidget' => $totalPengguna, 'iconWidget' => 'fa-user'],
            'aktif' => ['urlWidget' => (Auth::check() ? url($this->baseRoute.'/pengguna?akses_mobile=1') : ''), 'titleWidget' => 'Pengguna Aktif', 'classWidget' => 'col-lg-3', 'classBackgroundWidget' => 'bg-success', 'totalWidget' => $totalPenggunaAktif, 'iconWidget' => 'fa-shopping-cart'],
            'desa' => ['urlWidget' => url($this->baseRoute.'/desa'), 'titleWidget' => 'Total Desa', 'classWidget' => 'col-lg-3', 'classBackgroundWidget' => 'bg-primary', 'totalWidget' => $totalDesaPengguna, 'iconWidget' => 'fa-user'],
            'desa_aktif' => ['urlWidget' => url($this->baseRoute.'/desa?akses_mobile=1'), 'titleWidget' => 'Desa pengguna Aktif', 'classWidget' => 'col-lg-3', 'classBackgroundWidget' => 'bg-warning', 'totalWidget' => $totalDesaPenggunaAktif, 'iconWidget' => 'fa-shopping-cart'],
        ];

        $desaWidgetsKelolaDesa = [
            'semua' => ['urlWidget' => (Auth::check() ? url($this->baseRoute.'/pengguna') : ''), 'titleWidget' => 'Total Pengguna', 'classWidget' => 'col-lg-3', 'classBackgroundWidget' => 'bg-info', 'totalWidget' => $totalPenggunaKelolaDesa, 'iconWidget' => 'fa-user'],
            'aktif' => ['urlWidget' => (Auth::check() ? url($this->baseRoute.'/pengguna?akses_mobile=1') : ''), 'titleWidget' => 'Pengguna Aktif', 'classWidget' => 'col-lg-3', 'classBackgroundWidget' => 'bg-success', 'totalWidget' => $totalPenggunaAktifKelolaDesa, 'iconWidget' => 'fa-shopping-cart'],
            'desa' => ['urlWidget' => url($this->baseRoute.'/desa'), 'titleWidget' => 'Total Desa', 'classWidget' => 'col-lg-3', 'classBackgroundWidget' => 'bg-primary', 'totalWidget' => $totalDesaPenggunaKelolaDesa, 'iconWidget' => 'fa-user'],
            'desa_aktif' => ['urlWidget' => url($this->baseRoute.'/desa?akses_mobile=1'), 'titleWidget' => 'Desa pengguna Aktif', 'classWidget' => 'col-lg-3', 'classBackgroundWidget' => 'bg-warning', 'totalWidget' => $totalDesaPenggunaAktifKelolaDesa, 'iconWidget' => 'fa-shopping-cart'],
        ];

        $penggunaBaru = $this->mobile->wilayahKhusus()->selectRaw('kode_desa, count(kode_desa) as jumlah')->with(['desa'])
            ->groupBy('kode_desa')
            ->where('created_at', '>=', now()->subDay(7))->get();

        $penggunaBaruKelolaDesa = $this->kelolaDesa->wilayahKhusus()->selectRaw('kode_desa, count(kode_desa) as jumlah')->with(['desa'])
            ->groupBy('kode_desa')
            ->where('created_at', '>=', now()->subDay(7))->get();

        return view($this->baseView.'.dashboard', [
            'baseRoute' => $this->baseRoute,
            'baseView' => $this->baseView,
            'desaWidgets' => $desaWidgets,
            'daftar_baru' => $penggunaBaru,
            'desaWidgetsKelolaDesa' => $desaWidgetsKelolaDesa,
            'daftar_baruKelolaDesa' => $penggunaBaruKelolaDesa,
        ]);
    }

    public function pengguna(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'akses_mobile' => $request->akses_mobile,
        ];

        if ($request->ajax()) {
            return DataTables::of(TrackMobile::wilayahKhusus()->filter($request)->with(['desa']))
                ->addIndexColumn()
                ->make(true);
        }

        return view($this->baseView.'.pengguna', compact('fillters'));
    }

    public function penggunaKelolaDesa(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'akses_mobile' => $request->akses_mobile,
        ];

        if ($request->ajax()) {
            return DataTables::of(TrackKeloladesa::wilayahKhusus()->filter($request)->with(['desa']))
                ->addIndexColumn()
                ->make(true);
        }

        return view($this->baseView . '.penggunakeloladesa', compact('fillters'));
    }


    public function desa(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'akses_mobile' => $request->akses_mobile,
        ];
        if ($request->ajax()) {
            return DataTables::of(Desa::leftJoin('track_mobile', 'desa.kode_desa', '=', 'track_mobile.kode_desa')
                ->leftJoin('track_keloladesa', 'desa.kode_desa', '=', 'track_keloladesa.kode_desa')
                ->groupBy('desa.kode_desa', 'desa.nama_kecamatan', 'desa.nama_kabupaten', 'desa.nama_provinsi', 'desa.nama_desa')
                ->havingRaw('COUNT(track_mobile.id) > 0 OR COUNT(track_keloladesa.id_device) > 0')
                ->selectRaw('COUNT(track_mobile.id) as count_track_mobile, COUNT(track_keloladesa.id_device) as count_track_keloladesa, desa.kode_desa, desa.nama_kecamatan, desa.nama_kabupaten, desa.nama_provinsi, desa.nama_desa')
                ->when($request['kode_provinsi'], function ($q) use ($request) {
                    $q->whereRaw('left(desa.kode_desa, 2) = '.$request['kode_provinsi']);
                })
                ->when($request['kode_kabupaten'], function ($q) use ($request) {
                    $q->whereRaw('left(kode_desa, 5) = '.$request['kode_kabupaten']);
                })
                ->when($request['kode_kecamatan'], function ($q) use ($request) {
                    $q->whereRaw('left(kode_desa, 8) = '.$request['kode_kecamatan']);
                })
                ->when(! empty($request['akses_mobile']), function ($query) use ($request) {
                    $interval = 'interval '.TrackMobile::ACTIVE_DAYS.' day';
                    $sign = '>=';
                    switch ($request['akses_mobile']) {
                        case '1':
                            $interval = 'interval '.TrackMobile::ACTIVE_DAYS.' day';
                            break;
                        case '2':
                            $interval = 'interval 2 month';
                            break;
                        case '3':
                            $interval = 'interval 2 month';
                            $sign = '<=';
                            break;
                    }

                    return $query->whereRaw('tgl_akses '.$sign.' now() - '.$interval);
                })
                ->wilayahKhusus())

                ->make(true);
        }

        return view($this->baseView.'.desa', compact('fillters'));
    }
}
