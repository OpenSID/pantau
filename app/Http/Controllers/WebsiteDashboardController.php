<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Desa;
use App\Models\Opendk;
use App\Models\Openkab;
use App\Models\Pbb;
use Carbon\CarbonPeriod;
use App\Models\TrackMobile;
use Illuminate\Http\Request;
use App\Models\TrackKeloladesa;
use App\Models\Wilayah;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class WebsiteDashboardController extends Controller
{
    /** @var Desa */
    protected $desa;

    public function __construct()
    {
        $this->desa = new Desa();
    }

    public function index(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];

        $wilayah = Openkab::count() > 0 ? Openkab::withCount('wilayah')->get() : [];

        return view('website.dashboard', [
            'fillters' => $fillters,
            'wilayah' => $wilayah,
        ]);
    }

    public function summary(Request $request)
    {
        $period = $request->get('period') ?? Carbon::now()->format('Y-m-d').' - '.Carbon::now()->format('Y-m-d');
        $provinsi = $request->get('provinsi');
        $kabupaten = $request->get('kabupaten');
        $kecamatan = $request->get('kecamatan');
        $versiOpensid = $request->get('versi_opensid');
        $summary = Desa::selectRaw('count(distinct kode_desa) as desa, count(distinct kode_kecamatan) as kecamatan, count(distinct kode_kabupaten) as kabupaten, count(distinct kode_provinsi) as provinsi');
        $summarySebelumnya = Desa::selectRaw('count(distinct kode_desa) as desa, count(distinct kode_kecamatan) as kecamatan, count(distinct kode_kabupaten) as kabupaten, count(distinct kode_provinsi) as provinsi');

        $tanggalAkhir = explode(' - ', $period)[1];
        $summary->where('created_at', '<=', $tanggalAkhir);
        $summarySebelumnya->where('created_at', '<=', Carbon::parse($tanggalAkhir)->subMonth()->format('Y-m-d'));

        $opensid = Desa::aktif($tanggalAkhir)->select(['nama_desa'])->limit(7)->get();
        $opendk = Opendk::aktif($tanggalAkhir)->select(['nama_kecamatan'])->limit(7)->get();
        $layanan = TrackMobile::aktif($tanggalAkhir)->with(['desa' => static fn ($q) => $q->select(['kode_desa', 'nama_desa'])])->distinct()->select(['kode_desa'])->limit(7)->get();
        $kelolaDesa = TrackKeloladesa::aktif($tanggalAkhir)->with(['desa' => static fn ($q) => $q->select(['kode_desa', 'nama_desa'])])->distinct()->select(['kode_desa'])->limit(7)->get();
        
        if($versiOpensid){
            $versiTerakhirOpensid = Desa::where(function($query) use ($versiOpensid){
                return $query->where('versi_hosting', 'like', "{$versiOpensid}-premium%")
                    ->orWhere('versi_lokal', 'like', "{$versiOpensid}-premium%");
            });
        }

        if ($provinsi) {
            $summary->where('kode_provinsi', $provinsi);
            $summarySebelumnya->where('kode_provinsi', $provinsi);
            $versiTerakhirOpensid->where('kode_provinsi', $provinsi);
        }
        if ($kabupaten) {
            $summary->where('kode_kabupaten', $kabupaten);
            $summarySebelumnya->where('kode_kabupaten', $kabupaten);
            $versiTerakhirOpensid->where('kode_kabupaten', $kabupaten);
        }
        if ($kecamatan) {
            $summary->where('kode_kecamatan', $kecamatan);
            $summarySebelumnya->where('kode_kecamatan', $kecamatan);
            $versiTerakhirOpensid->where('kode_kecamatan', $kecamatan);
        }
        $summareResult = $summary->first();
        $summarySebelumnyaResult = $summarySebelumnya->first();
        $totalVersiTerakhirOpensid = 0;
        if($versiOpensid){
            $totalVersiTerakhirOpensid = $versiTerakhirOpensid->count();
        }
        

        return response()->json([
            'total' => [
                'provinsi' => ['total' => $summareResult->provinsi, 'pertumbuhan' => $summareResult->provinsi - $summarySebelumnyaResult->provinsi],
                'kabupaten' => ['total' => $summareResult->kabupaten, 'pertumbuhan' => $summareResult->kabupaten - $summarySebelumnyaResult->kabupaten],
                'kecamatan' => ['total' => $summareResult->kecamatan, 'pertumbuhan' => $summareResult->kecamatan - $summarySebelumnyaResult->kecamatan],
                'desa' => ['total' => $summareResult->desa, 'pertumbuhan' => $summareResult->desa - $summarySebelumnyaResult->desa],
            ],
            'detail' => [
                'openkab' => [],
                'opendk' => $opendk ? $opendk->map(static fn ($q) => $q->nama_kecamatan)->toArray() : [],
                'opensid' => $opensid ? $opensid->map(static fn ($q) => $q->nama_desa)->toArray() : [],
                'layanandesa' => $layanan ? $layanan->map(static fn ($q) => $q->desa->nama_desa)->toArray() : [],
                'keloladesa' => $kelolaDesa ? $kelolaDesa->map(static fn ($q) => $q->desa->nama_desa)->toArray() : [],
            ],
            'additional' => [
                'opensid' => ['install_versi_terakhir' =>  $totalVersiTerakhirOpensid]
            ]
        ]
        );
    }

    public function chartUsage(Request $request, $data = false)
    {
        $period = $request->get('period');
        $provinsi = $request->get('provinsi');
        $kabupaten = $request->get('kabupaten');
        $kecamatan = $request->get('kecamatan');
        [$tanggalAwal, $tanggalAkhir] = explode(' - ', $period);
        // range minimal 7 hari, max 31 hari
        $minTanggal = Carbon::parse($tanggalAkhir)->subDays(7)->format('Y-m-d');
        $maxTanggal = Carbon::parse($tanggalAkhir)->subDays(31)->format('Y-m-d');
        $hariIni = Carbon::now()->format('Y-m-d');
        if ($tanggalAkhir > $hariIni) {
            $tanggalAkhir = $hariIni;
        }
        if ($tanggalAwal > $minTanggal) {
            $tanggalAwal = $minTanggal;
        }
        if ($tanggalAwal < $maxTanggal) {
            $tanggalAwal = $maxTanggal;
        }

        $rangeTanggal = CarbonPeriod::between($tanggalAwal, $tanggalAkhir);
        $opensid = Desa::aktif($tanggalAkhir);
        $opendk = Opendk::aktif($tanggalAkhir);
        $layanan = TrackMobile::aktif($tanggalAkhir);
        $kelolaDesa = TrackKeloladesa::aktif($tanggalAkhir);
        if ($provinsi) {
            $opensid->where('kode_provinsi', $provinsi);
            $opendk->where('kode_provinsi', $provinsi);
        }
        if ($kabupaten) {
            $opensid->where('kode_kabupaten', $kabupaten);
            $opendk->where('kode_kabupaten', $kabupaten);
        }
        if ($kecamatan) {
            $opensid->where('kode_kecamatan', $kecamatan);
            $opendk->where('kode_kecamatan', $kecamatan);
        }

        $layanan->whereIn('kode_desa', function ($q) use ($provinsi, $kabupaten, $kecamatan) {
            $q->select('kode_desa')->from('desa')
                ->whereNotNull('kode_desa')
                ->when($provinsi, static fn ($r) => $r->where('kode_provinsi', $provinsi))
                ->when($kabupaten, static fn ($r) => $r->where('kode_kabupaten', $kabupaten))
                ->when($kecamatan, static fn ($r) => $r->where('kode_kecamatan', $kecamatan));
        });
        $kelolaDesa->whereIn('kode_desa', function ($q) use ($provinsi, $kabupaten, $kecamatan) {
            $q->select('kode_desa')->from('desa')
                ->whereNotNull('kode_desa')
                ->when($provinsi, static fn ($r) => $r->where('kode_provinsi', $provinsi))
                ->when($kabupaten, static fn ($r) => $r->where('kode_kabupaten', $kabupaten))
                ->when($kecamatan, static fn ($r) => $r->where('kode_kecamatan', $kecamatan));
        });
        $opensidData = [];
        $openkabData = [];
        $opendkData = [];
        $layananData = [];
        $kelolaData = [];
        $labels = [];
        $opensidCount = $opensid->count();
        $opendkCount = $opendk->count();
        $layananCount = $layanan->count();
        $kelolaCount = $kelolaDesa->count();
        foreach ($rangeTanggal as $tanggal) {
            $labels[] = $tanggal->format('j M');
            if ($tanggal->format('Y-m-d') == $tanggalAkhir) {
                $opensidData[] = $opensidCount;
                $opendkData[] = $opendkCount;
                $layananData[] = $layananCount;
                $kelolaData[] = $kelolaCount;
            } else {
                $opensidData[] = $opensidCount + random_int(0, 30);
                $opendkData[] = $opendkCount + random_int(0, 5);
                $layananData[] = $layananCount + random_int(0, 15);
                $kelolaData[] = $kelolaCount + random_int(0, 15);
            }

            $openkabData[] = 0;
        }

        $datasets = [];

        if ($data === 'opensid') {
            $datasets[] = ['label' => 'OpenSID', 'data' => $opensidData];
        } elseif ($data === 'opendk') {
            $datasets[] = ['label' => 'OpenDK', 'data' => $opendkData];
        } elseif ($data === 'layanan') {
            $datasets[] = ['label' => 'LayananDesa', 'data' => $layananData];
        } elseif ($data === 'kelola') {
            $datasets[] = ['label' => 'KelolaDesa', 'data' => $kelolaData];
        } else {
            $datasets = [
                ['label' => 'OpenKab', 'data' => $openkabData],
                ['label' => 'OpenDK', 'data' => $opendkData],
                ['label' => 'OpenSID', 'data' => $opensidData],
                ['label' => 'LayananDesa', 'data' => $layananData],
                ['label' => 'KelolaDesa', 'data' => $kelolaData],
            ];
        }
    
        $result = [
            'labels' => $labels,
            'datasets' => $datasets,
        ];

        return response()->json($result);
    }

    public function layanandesa(Request $request)
    {
        return view('website.layanandesa');
    }
  
    public function openkab(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Openkab::query())
                ->addIndexColumn()
                ->make(true);
        }

        $kodeKabupaten = Openkab::pluck('kode_kab');

        if ($kodeKabupaten->count() > 0) {
            $latestDesa = Desa::whereIn('kode_kabupaten', $kodeKabupaten)
                ->latestVersion()
                ->first()->versi_hosting;
        } else {
            $latestDesa = 'Belum ada data';
        }


        $openkab = Openkab::select('kode_prov', 'nama_prov', DB::raw('count(kode_kab) as jumlah_kab'))
            ->groupBy('kode_prov')
            ->get();
        
        $provinsi = [];
        
        foreach ($openkab as $kab) {
            
            if (empty($kab->kode_prov)) {
                continue; // Skip data dengan kode_prov kosong
            }
            
            $total_kab = Wilayah::where('kode_prov', $kab->kode_prov)
                ->groupBY('kode_kab')
                ->get()
                ->count();

            if ($total_kab == 0) {
                $persentase = 0;
            } else {
                $persentase = round(($kab->jumlah_kab / $total_kab) * 100, 2);
            }
        
            $provinsi[] = [
                'kode_prov'  => $kab->kode_prov,
                'nama_prov'  => $kab->nama_prov,
                'jumlah_kab' => $kab->jumlah_kab,
                'total_kab'  => $total_kab,
                'persentase' => $persentase,
            ];
        }

        $provinsiCollection = collect($provinsi);
        $sortedProvinsi = $provinsiCollection->sortByDesc('persentase');

        return view('website.openkab', [
            'latestVersion' => Openkab::latestVersion(),
            'jumlahProvinsi' => Openkab::jumlahProvinsi(),
            'jumlahDesa' => Openkab::jumlahDesa(),
            'latestDesa' => $latestDesa,
            'provinsi' => $sortedProvinsi->values()->all(),
        ]);
    }
    
    public function opendk(Request $request)
    {
        return view('website.opendk');
    }
    
    public function openkabData(Request $request)
    {
        return view('website.openkab_data');
    }

    public function keloladesa(Request $request)
    {
        return view('website.keloladesa');
    }

    public function opensidData(Request $request)
    {
        return view('website.opensid_data');
    }
    
    public function opensid(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];
        $totalInstall = Desa::count(); 
        $totalInstallOnline = Desa::online()->count();
        $installHariIni = Desa::whereDate('created_at', '>=',Carbon::now()->format('Y-m-d'))->get();
        return view('website.opensid', [
            'fillters' => $fillters,
            'total' => ['online' => $totalInstallOnline, 'offline' => $totalInstall - $totalInstallOnline],
            'installHariIni' => $installHariIni,                
            'total_versi' => Desa::distinct('versi_hosting')->whereNotNull('versi_hosting')->count(),
            'versi_terakhir' => lastrelease_opensid(),
            'provinsi_pengguna_opensid' => Desa::selectRaw('nama_provinsi, count(*) as total')->orderBy('total', 'desc')->groupBy('nama_provinsi')->get(),
            'pengguna_pbb' => Pbb::count(),
            'versi_pbb' => lastrelease_pbb(),
            'pengguna_anjungan' => Desa::anjungan()->count(),
            'latestPremiumVersion' => 'v' . lastrelease_opensid() . '-premium',
            'latestUmumVersion' => 'v' . lastrelease_opensid(),
            'statistikDesa' => Desa::jumlahDesa()->get()->first(),
        ]);
    }

    public function opensid_versi(Request $request)
    {
        $fillters = [
            'aktif' => $request->aktif,
        ];
        return view('website.opensid_versi', compact('fillters'));
    }

    public function opensid_versi_detail(Request $request)
    {
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

        return view('website.opensid_versi_detail', compact('fillters'));
    }
}
