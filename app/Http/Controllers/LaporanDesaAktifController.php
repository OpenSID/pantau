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

            return DataTables::of(Desa::fillter($fillters)->withCount(['akses' => static fn ($q) => $q->where('created_at', '>=', $_30HariLalu)])->where('updated_at', '>=', $_30HariLalu))
                ->addIndexColumn()
                ->make(true);
        }

        return view('laporan.desa_aktif', compact('fillters'));
    }
}
