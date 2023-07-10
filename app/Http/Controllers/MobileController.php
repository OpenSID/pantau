<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\TrackMobile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Yajra\DataTables\Facades\DataTables;

class MobileController extends Controller
{
    private $mobile;

    protected $baseRoute = 'mobile';

    protected $baseView = 'mobile';

    public function __construct()
    {
        $this->mobile = new TrackMobile();
        Config::set('title', $this->baseView.'');
    }

    public function index()
    {
        $totalPengguna = $this->mobile->wilayahKhusus()->count();
        $totalDesaPengguna = $this->mobile->wilayahKhusus()->desa()->count();
        $totalDesaPenggunaAktif = $this->mobile->wilayahKhusus()->desa()->active()->count();
        $totalPenggunaAktif = $this->mobile->wilayahKhusus()->count();

        $desaWidgets = [
            'semua' => ['urlWidget' => url($this->baseRoute.'/pengguna'), 'titleWidget' => 'Total Pengguna', 'classWidget' => 'col-lg-3', 'classBackgroundWidget' => 'bg-info', 'totalWidget' => $totalPengguna, 'iconWidget' => 'fa-user'],
            'aktif' => ['urlWidget' => url($this->baseRoute.'/pengguna?akses_mobile=1'), 'titleWidget' => 'Pengguna Aktif', 'classWidget' => 'col-lg-3', 'classBackgroundWidget' => 'bg-success', 'totalWidget' => $totalPenggunaAktif, 'iconWidget' => 'fa-shopping-cart'],
            'desa' => ['urlWidget' => url($this->baseRoute.'/desa'), 'titleWidget' => 'Total Desa', 'classWidget' => 'col-lg-3', 'classBackgroundWidget' => 'bg-primary', 'totalWidget' => $totalDesaPengguna, 'iconWidget' => 'fa-user'],
            'desa_aktif' => ['urlWidget' => url($this->baseRoute.'/desa?akses_mobile=1'), 'titleWidget' => 'Desa pengguna Aktif', 'classWidget' => 'col-lg-3', 'classBackgroundWidget' => 'bg-warning', 'totalWidget' => $totalDesaPenggunaAktif, 'iconWidget' => 'fa-shopping-cart'],
        ];

        return view($this->baseView.'.dashboard', [
            'baseRoute' => $this->baseRoute,
            'baseView' => $this->baseView,
            'desaWidgets' => $desaWidgets,
            'daftar_baru' => $this->mobile->wilayahKhusus()->with(['desa'])->where('created_at', '>=', now()->subDay(7))->get(),
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
            return DataTables::of(TrackMobile::wilayahKhusus()->filter($request)->with(['desa'])->get())
                ->addIndexColumn()
                ->make(true);
        }

        return view($this->baseView.'.pengguna', compact('fillters'));
    }

    public function desa(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'akses_mobile' => $request->akses_mobile,
        ];
        if ($request->ajax()) {
            return DataTables::of(Desa::whereHas('mobile', function (Builder $query) use ($request) {
                $query->when($request['kode_provinsi'], function ($q) use ($request) {
                    $q->whereRaw('left(kode_desa, 2) = '.$request['kode_provinsi']);
                });
                $query->when($request['kode_kabupaten'], function ($q) use ($request) {
                    $q->whereRaw('left(kode_desa, 5) = '.$request['kode_kabupaten']);
                });
                $query->when($request['kode_kecamatan'], function ($q) use ($request) {
                    $q->whereRaw('left(kode_desa, 8) = '.$request['kode_kecamatan']);
                });
                $query->when(! empty($request['akses_mobile']), function ($query) use ($request) {
                    $interval = 'interval '.TrackMobile::ACTIVE_DAYS.' day';
                    $sign = '>=';
                    switch($request['akses_mobile']) {
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
                });
            })->wilayahKhusus()->with(['mobile' => function ($r) {
                $r->select('kode_desa');
            }])->get())
                ->addColumn('jumlah', function ($data) {
                    return $data->mobile->count();
                })
                ->make(true);
        }

        return view($this->baseView.'.desa', compact('fillters'));
    }
}
