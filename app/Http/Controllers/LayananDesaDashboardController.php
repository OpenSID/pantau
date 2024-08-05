<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\TrackMobile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LayananDesaDashboardController extends Controller
{
    public function index(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];
        $versiTerakhir = lastrelease_api_layanandesa();
        $installHariIni = TrackMobile::with(['desa'])->whereDate('created_at', '>=',Carbon::now()->format('Y-m-d'))->get();
        return view('website.layanandesa.index', [
            'fillters' => $fillters,
            'total_versi' => 2,
            'total_desa' => format_angka(Desa::count()),
            'pengguna_layanan_desa' => TrackMobile::distinct('kode_desa')->count(),
            'versi_terakhir' => $versiTerakhir,
            'info_rilis' => 'Rilis LayananDesa '.$versiTerakhir,
            'total_versi' => TrackMobile::distinct('versi')->count(),
            'pengguna_versi_terakhir' => TrackMobile::where('versi', $versiTerakhir)->count(),
            'installHariIni' => $installHariIni
        ]);
    }

    public function detail()
    {
        return view('website.layanandesa.detail');
    }

    public function versi(Request $request)
    {     
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];

        if ($request->ajax()) {
            return DataTables::of(TrackMobile::groupBy('versi')->selectRaw('versi, count(*) as jumlah'))                
                ->addIndexColumn()
                ->make(true);
        }

        return view('website.layanandesa.versi_lengkap', compact('fillters'));
    }

    public function versi_detail(Request $request)
    {                
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,
        ];
        
        if ($request->ajax()) {
            $versi = $request->versi;
            return DataTables::of(TrackMobile::filter($fillters)->when($versi, static fn($q) => $q->where('versi', $versi))->with(['desa'])->groupBy(['versi', 'kode_desa'])->selectRaw('kode_desa, versi, count(*) as jumlah'))
                ->addIndexColumn()
                ->make(true);
        }

        return view('website.layanandesa.versi_detail', compact('fillters'));
    }

    public function install_baru(Request $request)
    {        
        if ($request->ajax()) {
            return DataTables::of(TrackMobile::with('desa')->whereDate('created_at', '>=', Carbon::now()->subDays(7)))
                ->editColumn('updated_at', static fn($q) => $q->updated_at->translatedFormat('j F Y H:i'))
                ->addIndexColumn()
                ->make(true);
        }        
    }    
}
