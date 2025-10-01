<?php

namespace App\Http\Controllers;

use App\Models\Openkab;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LaporanOpenkabController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Openkab::query();

            // Apply filter based on request parameter
            $filter = $request->query('filter');

            if ($filter === 'provinsi') {
                // Group by province to show only one record per province
                $query = Openkab::select('kode_prov', 'nama_prov')
                    ->selectRaw('COUNT(*) as jumlah_kabupaten')
                    ->selectRaw('MAX(tgl_rekam) as tgl_rekam')
                    ->selectRaw('MAX(url) as url')
                    ->selectRaw('MAX(versi) as versi')
                    ->selectRaw('"OpenKab" as nama_aplikasi')
                    ->groupBy('kode_prov', 'nama_prov');
            } elseif ($filter === 'terpasang') {
                // Only show kabupaten with installed versions
                $query->where('versi', '!=', '')->whereNotNull('versi');
            }
            // For 'total' or no filter, show all data

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('tgl_rekam', function ($row) {
                    return $row->tgl_rekam ? date('d/m/Y H:i', strtotime($row->tgl_rekam)) : '-';
                })
                ->editColumn('url', function ($row) {
                    return $row->url ? '<a href="' . $row->url . '" target="_blank">' . $row->url . '</a>' : '-';
                })
                ->editColumn('nama_wilayah', function ($row) use ($filter) {
                    if ($filter === 'provinsi') {
                        return $row->nama_prov . ' (' . $row->jumlah_kabupaten . ' kabupaten)';
                    }
                    return isset($row->nama_wilayah) ? $row->nama_wilayah : $row->nama_kab;
                })
                ->rawColumns(['url'])
                ->make(true);
        }

        // Statistik dashboard
        $jumlahProvinsi = Openkab::jumlahProvinsi();
        $totalKabupaten = Openkab::count();
        $kabupatenTerpasang = Openkab::where('versi', '!=', '')->whereNotNull('versi')->count();

        return view('laporan.openkab', compact('jumlahProvinsi', 'totalKabupaten', 'kabupatenTerpasang'));
    }

    public function pengguna(Request $request)
    {
        if ($request->ajax()) {
            $query = Openkab::query()
                ->selectRaw('openkab.*')
                ->selectRaw('CASE 
                    WHEN openkab.updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                    THEN "Aktif" 
                    ELSE "Tidak Aktif" 
                END as status_akses')
                ->selectRaw('COALESCE(openkab.jumlah_desa, 0) as jumlah_pengguna_terdaftar')
                ->selectRaw('openkab.tgl_rekam as login_terakhir');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('nama_kab', function ($row) {
                    return $row->nama_kab;
                })
                ->addColumn('status_akses_display', function ($row) {
                    if ($row->status_akses === 'Aktif') {
                        return '<span class="badge badge-success">Aktif</span>';
                    }
                    return '<span class="badge badge-danger">Tidak Aktif</span>';
                })
                ->editColumn('login_terakhir', function ($row) {
                    return $row->login_terakhir ? date('d/m/Y H:i', strtotime($row->login_terakhir)) : '-';
                })
                ->editColumn('jumlah_pengguna_terdaftar', function ($row) {
                    return number_format($row->jumlah_pengguna_terdaftar);
                })
                ->filterColumn('status_akses', function ($query, $keyword) {
                    $query->whereRaw("(CASE 
                        WHEN openkab.updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                        THEN 'Aktif' 
                        ELSE 'Tidak Aktif' 
                    END) like ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['status_akses_display'])
                ->make(true);
        }

        return view('laporan.openkab-pengguna');
    }
}
