<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        return view('website.dashboard', [
            'fillters' => $fillters,
        ]);
    }

    public function summary(Request $request)
    {
        $period = $request->get('period') ?? Carbon::now()->format('Y-m-d').' - '.Carbon::now()->format('Y-m-d');
        $provinsi = $request->get('provinsi');
        $kabupaten = $request->get('kabupaten');
        $kecamatan = $request->get('kecamatan');
        $summary = Desa::selectRaw('count(distinct kode_desa) as desa, count(distinct kode_kecamatan) as kecamatan, count(distinct kode_kabupaten) as kabupaten, count(distinct kode_provinsi) as provinsi');
        $summarySebelumnya = Desa::selectRaw('count(distinct kode_desa) as desa, count(distinct kode_kecamatan) as kecamatan, count(distinct kode_kabupaten) as kabupaten, count(distinct kode_provinsi) as provinsi');

        if ($period) {
            $tanggalAkhir = explode(' - ', $period)[1];
            $summary->where('created_at', '<=', $tanggalAkhir);
            $summarySebelumnya->where('created_at', '<=', Carbon::parse($tanggalAkhir)->subMonth()->format('Y-m-d'));
            $desaAktif = Desa::aktif($tanggalAkhir);
            $desaAktifOnline = Desa::aktifOnline($tanggalAkhir);
        }
        if ($provinsi) {
            $summary->where('kode_provinsi', $provinsi);
            $summarySebelumnya->where('kode_provinsi', $provinsi);
            $desaAktif->where('kode_provinsi', $provinsi);
            $desaAktifOnline->where('kode_provinsi', $provinsi);
        }
        if ($kabupaten) {
            $summary->where('kode_kabupaten', $kabupaten);
            $summarySebelumnya->where('kode_kabupaten', $kabupaten);
            $desaAktif->where('kode_kabupaten', $kabupaten);
            $desaAktifOnline->where('kode_kabupaten', $kabupaten);
        }
        if ($kecamatan) {
            $summary->where('kode_kecamatan', $kecamatan);
            $summarySebelumnya->where('kode_kecamatan', $kecamatan);
            $desaAktif->where('kode_kecamatan', $kecamatan);
            $desaAktifOnline->where('kode_kecamatan', $kecamatan);
        }
        $summareResult = $summary->first();
        $summarySebelumnyaResult = $summarySebelumnya->first();

        return response()->json([
            'total' => [
                'provinsi' => ['total' => $summareResult->provinsi, 'pertumbuhan' => $summareResult->provinsi - $summarySebelumnyaResult->provinsi],
                'kabupaten' => ['total' => $summareResult->kabupaten, 'pertumbuhan' => $summareResult->kabupaten - $summarySebelumnyaResult->kabupaten],
                'kecamatan' => ['total' => $summareResult->kecamatan, 'pertumbuhan' => $summareResult->kecamatan - $summarySebelumnyaResult->kecamatan],
                'desa' => ['total' => $summareResult->desa, 'pertumbuhan' => $summareResult->desa - $summarySebelumnyaResult->desa],
                'desa_aktif' => ['total' => $desaAktif->count()],
                'desa_aktif_online' => ['total' => $desaAktifOnline->count()],
            ],
            'detail' => [
                'openkab' => [],
                'opendk' => [],
                'opensid' => [],
                'layanan_desa' => [],
                'kelola_desa' => [],
            ],
        ]
        );
    }

    public function chartUsage(Request $request)
    {
        $period = $request->get('period');
        $provinsi = $request->get('provinsi');
        $kabupaten = $request->get('kabupaten');
        $kecamatan = $request->get('kecamatan');
        $period = explode(' - ', '2024-06-10 - 2024-06-30');

        $result = [
            'labels' => [1, 2, 3, 4, 5],
            'datasets' => [
                ['label' => 'OpenKab', 'data' => [9, 14, 2, 6]],
                ['label' => 'OpenDK', 'data' => [19, 24]],
                ['label' => 'OpenSID', 'data' => [9, 4]],
                ['label' => 'LayananDesa', 'data' => [9, 1, 4]],
                ['label' => 'KelolaDesa', 'data' => [19, 4, 5]],
            ],
        ];

        return response()->json($result);
    }
}
