<?php

namespace App\Http\Controllers;

use App\Models\Opendk;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Yajra\DataTables\Facades\DataTables;

class OpendkController extends Controller
{
    private $opendk;

    protected $baseRoute = 'opendk';

    protected $baseView = 'opendk';

    public function __construct()
    {
        $this->opendk = new Opendk();
        Config::set('title', $this->baseView.'');
    }

    public function index()
    {
        $version = null;
        $versiOpensid = lastrelease('https://api.github.com/repos/OpenSID/opendk/releases/latest');

        if ($versiOpensid !== false) {
            $version = $versiOpensid->tag_name;
            $version = preg_replace('/[^0-9]/', '', $version);
            $version = substr($version, 0, 4);
        }

        $totalKecamatan = $this->opendk->wilayahkhusus()->count();
        $totalAktifKecamatan = $this->opendk->wilayahkhusus()->active()->count();
        $totalVersiTerbaruKecamatan = $version ? $this->opendk->wilayahkhusus()->versiTerbaru($version)->count() : 10;
        $totalKabupaten = $this->opendk->wilayahkhusus()->distinct('kode_kabupaten')->count();
        $totalAktifKabupaten = $this->opendk->wilayahkhusus()->active()->distinct('kode_kabupaten')->count();
        $totalVersiTerbaruKabupaten = $version ? $this->opendk->wilayahkhusus()->versiTerbaru($version)->distinct('kode_kabupaten')->count() : 0;
        $kecamatanWidgets = [
            'semua' => ['urlWidget' => url($this->baseRoute.'/kecamatan'), 'titleWidget' => 'Total Kecamatan', 'classWidget' => 'col-lg-4', 'classBackgroundWidget' => 'bg-info', 'totalWidget' => $totalKecamatan, 'iconWidget' => 'fa-shopping-cart'],
            'aktif' => ['urlWidget' => url($this->baseRoute.'/kecamatan?status=1'), 'titleWidget' => 'Kecamatan pengguna Aktif', 'classWidget' => 'col-lg-4', 'classBackgroundWidget' => 'bg-success', 'totalWidget' => $totalAktifKecamatan, 'iconWidget' => 'fa-shopping-cart'],
            'baru' => ['urlWidget' => url($this->baseRoute.'/kecamatan?versi='.$version), 'titleWidget' => 'Kecamatan Pengguna OpenDK Versi Terbaru ', 'classWidget' => 'col-lg-4', 'classBackgroundWidget' => 'bg-warning', 'totalWidget' => $totalVersiTerbaruKecamatan, 'iconWidget' => 'fa-user'],
        ];
        $kabupatenWidgets = [
            'semua' => ['urlWidget' => url($this->baseRoute.'/kabupaten'), 'titleWidget' => 'Total Kabupaten', 'classWidget' => 'col-lg-4', 'classBackgroundWidget' => 'bg-info', 'totalWidget' => $totalKabupaten, 'iconWidget' => 'fa-shopping-cart'],
            'aktif' => ['urlWidget' => url($this->baseRoute.'/kabupaten?status=1'), 'titleWidget' => 'Kabupaten pengguna Aktif', 'classWidget' => 'col-lg-4', 'classBackgroundWidget' => 'bg-success', 'totalWidget' => $totalAktifKabupaten, 'iconWidget' => 'fa-shopping-cart'],
            'baru' => ['urlWidget' => url($this->baseRoute.'/kabupaten?versi='.$version), 'titleWidget' => 'Kabupaten Pengguna OpenDK Versi Terbaru ', 'classWidget' => 'col-lg-4', 'classBackgroundWidget' => 'bg-warning', 'totalWidget' => $totalVersiTerbaruKabupaten, 'iconWidget' => 'fa-user'],
        ];

        return view($this->baseView.'.dashboard', [
            'baseRoute' => $this->baseRoute,
            'baseView' => $this->baseView,
            'kecamatanWidgets' => $kecamatanWidgets,
            'kabupatenWidgets' => $kabupatenWidgets,
            'daftar_baru' => $this->opendk->wilayahkhusus()->where('created_at', '>=', now()->subDay(7))->get(),
        ]);
    }

    public function versi(Request $request)
    {
        $fillters = [
            'aktif' => $request->aktif,
        ];
        if ($request->ajax()) {
            return DataTables::of(Opendk::versi()->get())
                ->addIndexColumn()
                ->make(true);
        }

        return view($this->baseView.'.versi', compact('fillters'));
    }

    public function kecamatan(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'akses' => $request->akses,
        ];

        if ($request->ajax()) {
            return DataTables::of(Opendk::wilayahkhusus()->kecamatan($request)->selectRaw('updated_at as format_updated_at')->get())
                ->addIndexColumn()
                ->make(true);
        }

        return view($this->baseView.'.kecamatan', compact('fillters'));
    }

    public function peta(Request $request)
    {
        if ($request->ajax()) {
            $geoJSONdata = Opendk::get()->map(function ($kec) {
                $kec->content = "
                    <h6 class='text-center'><b style='color:red'>{$kec->sebutan_wilayah} {$kec->nama_kecamatan}</b></h6>
                    <b><table width='100%'>
                        <tbody><tr>
                            <td>{$kec->sebutan_wilayah}</td><td> : {$kec->sebutan_wilayah} Batu Berapit</td>
                        </tr>

                        <tr>
                        <td>Kab/Kota</td><td> : {$kec->nama_kabupaten}</td>
                        </tr>
                        <tr>
                            <td>Provinsi</td><td> : {$kec->nama_provinsi}</td>
                        </tr>
                        <tr>
                            <td>Jumlah Desa</td><td> : {$kec->jumlah_desa}</td>
                        </tr>

                        <tr>
                            <td>Batas Wilayah</td><td> : {$kec->batas_wilayah}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td><td> : {$kec->alamat}</td>
                        </tr>
                        <tr>
                            <td>Website</td><td> : <a href='http://{$kec->url}' target='_blank'>{$kec->url}</a></td>
                        </tr>
                    </tbody></table></b>
                ";

                return $kec;
            });

            return response()->json(
                $geoJSONdata,
            );
        }

        return view($this->baseView.'.peta');
    }

    public function kabupatenKosong(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Region::doesntHave('opendk')->with(['child' => function ($r) {
            $r->select('id', 'parent_code');
            }])->kabupaten()->selectRaw('tbl_regions.region_code as region_code')->get())
                ->addIndexColumn()
                ->addColumn('jumlah', function ($data) {
                    return $data->child->count();
                })
                ->make(true);
        }
    }
}
