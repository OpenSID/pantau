<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\Opendk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KecamatanAktifOpendkController extends Controller
{
    public function index(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
            'akses_opendk' => $request->akses_opendk,
            'versi_opendk' => $request->versi_opendk,
        ];

        if ($request->ajax()) {
            $_30HariLalu = Carbon::now()->subDays(30);

            // Query untuk mendapatkan data kecamatan OpenDK yang aktif dalam 30 hari terakhir
            $query = Opendk::wilayahkhusus()
                ->where('updated_at', '>=', $_30HariLalu)
                ->filterDatatable($fillters)
                ->select([
                    'kode_kecamatan',
                    'nama_kecamatan',
                    'nama_kabupaten',
                    'nama_provinsi',
                    'updated_at',
                ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('jumlah_desa', function ($data) {
                    // Hitung jumlah desa berdasarkan kode kecamatan
                    return Desa::where('kode_kecamatan', $data->kode_kecamatan)->count();
                })
                ->addColumn('akses_publik_30_hari', function ($data) use ($_30HariLalu) {
                    // Hitung total akses dalam 30 hari untuk desa di kecamatan ini
                    return Desa::where('kode_kecamatan', $data->kode_kecamatan)
                        ->withCount([
                            'akses' => function ($q) use ($_30HariLalu) {
                                $q->where('created_at', '>=', $_30HariLalu);
                            },
                        ])
                        ->get()
                        ->sum('akses_count');
                })
                ->addColumn('akses_admin_30_hari', function ($data) use ($_30HariLalu) {
                    // Untuk sementara gunakan data yang sama seperti akses publik
                    // karena tidak ada pembedaan admin/publik di tabel akses
                    return Desa::where('kode_kecamatan', $data->kode_kecamatan)
                        ->withCount([
                            'akses' => function ($q) use ($_30HariLalu) {
                                $q->where('created_at', '>=', $_30HariLalu);
                            },
                        ])
                        ->get()
                        ->sum('akses_count');
                })
                ->addColumn('jumlah_artikel', function ($data) {
                    // Hitung total artikel untuk desa di kecamatan ini
                    return Desa::where('kode_kecamatan', $data->kode_kecamatan)
                        ->sum('jml_artikel') ?? 0;
                })
                ->addColumn('akses_terakhir', function ($data) {
                    return $data->updated_at ? $data->updated_at->format('Y-m-d H:i:s') : '-';
                })
                ->make(true);
        }

        return view('opendk.kecamatan_aktif', compact('fillters'));
    }
}
