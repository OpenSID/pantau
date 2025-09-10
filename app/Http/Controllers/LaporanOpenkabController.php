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

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('tgl_rekam', function ($row) {
                    return $row->tgl_rekam ? date('d/m/Y H:i', strtotime($row->tgl_rekam)) : '-';
                })
                ->editColumn('url', function ($row) {
                    return $row->url ? '<a href="' . $row->url . '" target="_blank">' . $row->url . '</a>' : '-';
                })
                ->editColumn('nama_wilayah', function ($row) {
                    return $row->nama_wilayah;
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
}
