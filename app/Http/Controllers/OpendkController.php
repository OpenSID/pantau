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
            $version = cleanVersi($versiOpensid->tag_name);
        }

        $totalKecamatan = $this->opendk->wilayahkhusus()->count();
        $totalAktifKecamatan = $this->opendk->wilayahkhusus()->active()->count();
        $totalVersiTerbaruKecamatan = $version ? $this->opendk->wilayahkhusus()->versiTerbaru($version)->count() : 10;
        $totalKabupaten = $this->opendk->wilayahkhusus()->distinct('kode_kabupaten')->count();
        $totalAktifKabupaten = $this->opendk->wilayahkhusus()->active()->distinct('kode_kabupaten')->count();
        $totalVersiTerbaruKabupaten = $version ? $this->opendk->wilayahkhusus()->versiTerbaru($version)->distinct('kode_kabupaten')->count() : 0;
        $kecamatanWidgets = [
            'semua' => ['urlWidget' => url($this->baseRoute.'/kecamatan'), 'titleWidget' => 'Total Kecamatan', 'classWidget' => 'col-lg-4', 'classBackgroundWidget' => 'bg-info', 'totalWidget' => $totalKecamatan, 'iconWidget' => 'fa-shopping-cart'],
            'aktif' => ['urlWidget' => url($this->baseRoute.'/kecamatan?akses_opendk=1'), 'titleWidget' => 'Kecamatan pengguna Aktif', 'classWidget' => 'col-lg-4', 'classBackgroundWidget' => 'bg-success', 'totalWidget' => $totalAktifKecamatan, 'iconWidget' => 'fa-shopping-cart'],
            'baru' => ['urlWidget' => url($this->baseRoute.'/kecamatan?versi_opendk='.$version), 'titleWidget' => 'Kecamatan Pengguna OpenDK Versi Terbaru ', 'classWidget' => 'col-lg-4', 'classBackgroundWidget' => 'bg-warning', 'totalWidget' => $totalVersiTerbaruKecamatan, 'iconWidget' => 'fa-user'],
        ];
        $kabupatenWidgets = [
            'semua' => ['urlWidget' => url($this->baseRoute.'/kabupaten'), 'titleWidget' => 'Total Kabupaten', 'classWidget' => 'col-lg-4', 'classBackgroundWidget' => 'bg-info', 'totalWidget' => $totalKabupaten, 'iconWidget' => 'fa-shopping-cart'],
            'aktif' => ['urlWidget' => url($this->baseRoute.'/kabupaten?akses_opendk=1'), 'titleWidget' => 'Kabupaten pengguna Aktif', 'classWidget' => 'col-lg-4', 'classBackgroundWidget' => 'bg-success', 'totalWidget' => $totalAktifKabupaten, 'iconWidget' => 'fa-shopping-cart'],
            'baru' => ['urlWidget' => url($this->baseRoute.'/kabupaten?versi_opendk='.$version), 'titleWidget' => 'Kabupaten Pengguna OpenDK Versi Terbaru ', 'classWidget' => 'col-lg-4', 'classBackgroundWidget' => 'bg-warning', 'totalWidget' => $totalVersiTerbaruKabupaten, 'iconWidget' => 'fa-user'],
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
            'versi_opendk' => $request->versi_opendk,
        ];
        $listVersi = $this->getListVersion();
        if ($request->ajax()) {
            return DataTables::of(Opendk::wilayahkhusus()->versi($request)->get())
                ->addIndexColumn()
                ->make(true);
        }

        return view($this->baseView.'.versi', compact('fillters', 'listVersi'));
    }

    public function kecamatan(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'akses_opendk' => $request->akses_opendk,
            'versi_opendk' => $request->versi_opendk,
        ];
        $listVersi = $this->getListVersion();
        if ($request->ajax()) {
            return DataTables::of(Opendk::wilayahkhusus()->kecamatan($request)->selectRaw('updated_at as format_updated_at')->get())
                ->addIndexColumn()
                ->make(true);
        }

        return view($this->baseView.'.kecamatan', compact('fillters', 'listVersi'));
    }

    public function kabupaten(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'akses_opendk' => $request->akses_opendk,
            'versi_opendk' => $request->versi_opendk,
        ];
        $listVersi = $this->getListVersion();
        if ($request->ajax()) {
            return DataTables::of(Opendk::wilayahkhusus()->with(['childKecamatan' => function ($r) {
            $r->select('kode_kabupaten', 'kode_kecamatan');
            }])->kabupaten($request)->get())
                ->addColumn('jumlah', function ($data) {
                    \Log::error($data->toJson());

                    return $data->childKecamatan->count();
                })
                ->make(true);
        }

        return view($this->baseView.'.kabupaten', compact('fillters', 'listVersi'));
    }

    public function peta(Request $request)
    {
        if ($request->ajax()) {
            $fillters = [
                'kode_provinsi' => $request->kode_provinsi,
                'kode_kabupaten' => $request->kode_kabupaten,
                'kode_kecamatan' => $request->kode_kecamatan,
                'akses_opendk' => $request->akses,
                'versi_opendk' => $request->versi_lokal,
            ];

            $geoJSONdata = Opendk::wilayahkhusus()->filterDatatable($fillters)->get()->map(function ($kec) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            (float) $kec->lng,
                            (float) $kec->lat,
                        ],
                    ],
                    'properties' => $this->properties($kec),
                    'id' => $kec->id,
                ];
            });

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $geoJSONdata,
            ]);
        }

        return view($this->baseView.'.peta');
    }

    private function properties($kec)
    {
        $link = '';
        if (auth()->check()) {
            $link = '<tr><td>Website</td><td> : <a href="http://'.strtolower($kec->url).'" target="_blank">'.strtolower($kec->url_hosting).'</a></b></td></tr>';
        }

        return [
            'logo' => null,
            'popupContent' => '
                <h6 class="text-center"><b style="color:red">'.strtoupper($kec->sebutan_wilayah.' '.$kec->nama_kecamatan).'</b></h6>
                <b><table width="100%">
                    <tr>
                    <td>Kab/Kota</td><td> : '.ucwords($kec->nama_kabupaten).'</b></td>
                    </tr>
                    <tr>
                        <td>Provinsi</td><td> : '.ucwords($kec->nama_provinsi).'</b></td>
                    </tr>
                    <tr>
                        <td>Alamat</td><td> : '.($kec->alamat_kantor ?? '').'</b></td>
                    </tr>
                    '.$link.'
                </table></b>',
        ];
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

    private function getListVersion()
    {
        return Opendk::selectRaw('DISTINCT right((LEFT(replace(versi, \'.\',\'\'),5)),4) as versi')->get()->sortByDesc('versi')->map(function ($item) {
            return $item->versi;
        })->values()->all();
    }
}
