<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LaporanDesaAktifController extends Controller
{
    public function index(Request $request)
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

        if ($request->ajax()) {
            $_30HariLalu = Carbon::now()->subDays(30);

            $query = Desa::query()
                ->select([
                    'desa.id',
                    'desa.nama_desa',
                    'desa.jml_surat_tte',
                    'desa.jml_mandiri',
                    'desa.jml_artikel',
                    'desa.jml_dokumen',
                    'desa.kode_provinsi',
                    'desa.kode_kabupaten',
                    'desa.kode_kecamatan',
                    'desa.versi_lokal',
                    'desa.versi_hosting',
                    'desa.tgl_akses_lokal',
                    'desa.tgl_akses_hosting',
                    'desa.modul_tte',
                    'desa.updated_at',
                ])
                ->selectRaw(
                    '(SELECT COUNT(*) FROM akses WHERE akses.desa_id = desa.id AND akses.created_at >= ?) as akses_count',
                    [$_30HariLalu]
                )
                ->where('desa.updated_at', '>=', $_30HariLalu);

            $this->applyFilters($query, $fillters);

            return DataTables::of($query)
                ->addIndexColumn()
                ->make(true);
        }

        return view('laporan.desa_aktif', compact('fillters'));
    }

    /**
     * Apply filters directly without calling scopeFillter (which overrides SELECT with *).
     */
    private function applyFilters($query, array $fillters): void
    {
        $fillters = array_merge([
            'kode_provinsi' => null,
            'kode_kabupaten' => null,
            'kode_kecamatan' => null,
            'akses' => null,
            'status' => null,
            'versi_lokal' => null,
            'versi_hosting' => null,
            'tte' => null,
        ], $fillters);

        $query
            ->when($fillters['kode_provinsi'] ?? false, function ($q, $kode_provinsi) {
                $q->where('desa.kode_provinsi', $kode_provinsi);
            })
            ->when($fillters['kode_kabupaten'] ?? false, function ($q, $kode_kabupaten) {
                $q->where('desa.kode_kabupaten', $kode_kabupaten);
            })
            ->when($fillters['kode_kecamatan'] ?? false, function ($q, $kode_kecamatan) {
                $q->where('desa.kode_kecamatan', $kode_kecamatan);
            })
            ->when($fillters['status'] == 1, function ($q) {
                $q->whereNotNull('desa.versi_hosting')->whereNull('desa.versi_lokal');
            })
            ->when($fillters['status'] == 2, function ($q) {
                $q->whereNotNull('desa.versi_lokal')->whereNull('desa.versi_hosting');
            })
            ->when($fillters['status'] == 3, function ($q) {
                $q->where(function ($sub) {
                    $version = lastrelease_opensid();
                    $sub->where('desa.versi_hosting', 'LIKE', $version.'-premium%')
                        ->orWhere('desa.versi_lokal', 'LIKE', $version.'-premium%');
                });
            })
            ->when($fillters['akses'] == 1, function ($q) {
                $q->whereRaw('timestampdiff(month, greatest(coalesce(desa.tgl_akses_lokal, 0), coalesce(desa.tgl_akses_hosting, 0)), now()) > 1');
            })
            ->when($fillters['akses'] == 2, function ($q) {
                $q->whereRaw('timestampdiff(month, greatest(coalesce(desa.tgl_akses_lokal, 0), coalesce(desa.tgl_akses_hosting, 0)), now()) <= 1');
            })
            ->when($fillters['akses'] == 3, function ($q) {
                $q->whereRaw('timestampdiff(month, greatest(coalesce(desa.tgl_akses_lokal, 0), coalesce(desa.tgl_akses_hosting, 0)), now()) > 3');
            })
            ->when($fillters['akses'] == 4, function ($q) {
                $q->whereRaw('greatest(coalesce(desa.tgl_akses_lokal, 0), coalesce(desa.tgl_akses_hosting, 0)) >= now() - interval 7 day');
            })
            ->when($fillters['akses'] == 5, function ($q) {
                $q->whereRaw("desa.versi_lokal <> '' and desa.versi_hosting is null and coalesce(desa.tgl_akses_lokal, 0) >= now() - interval 7 day");
            })
            ->when($fillters['versi_lokal'], function ($q, $versi) {
                $q->where('desa.versi_lokal', $versi);
            })
            ->when($fillters['versi_hosting'], function ($q, $versi) {
                $q->where('desa.versi_hosting', $versi);
            })
            ->when(in_array($fillters['tte'], ['1', '0']), function ($q) use ($fillters) {
                $q->where('desa.modul_tte', $fillters['tte']);
            });
    }
}
