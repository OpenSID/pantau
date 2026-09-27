<?php

namespace App\Http\Controllers;

use App\Models\Pbb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PenggunaAktifPbbController extends Controller
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

            $query = Pbb::wilayahkhusus()
                ->where('updated_at', '>=', $_30HariLalu)
                ->filterDatatable($fillters)
                ->select([
                    'id',
                    'nama_desa',
                    'nama_kecamatan',
                    'nama_kabupaten',
                    'nama_provinsi',
                    'versi',
                    'updated_at',
                ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('akses_terakhir', function ($data) {
                    return $data->updated_at ? $data->updated_at->format('Y-m-d H:i:s') : '-';
                })
                ->make(true);
        }

        return view('pbb.pengguna_aktif', compact('fillters'));
    }
}
