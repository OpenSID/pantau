<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LaporanTemaController extends Controller
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

        $tema = $request->query('tema', '');

        if ($request->ajax()) {
            if($tema){
                $data = DataTables::of(Desa::fillter($fillters)->where('tema', $tema));
            }else{
                $data = DataTables::of(Desa::fillter($fillters)->whereIn('tema', ['esensi', 'natra', 'palanta']));
            }
            return $data->addIndexColumn()
            ->editColumn('updated_at', fn($q) => $q->updated_at->format('Y-m-d H:i:s'))
            ->editColumn('url_hosting', function ($q) {
                return '<a href="https://' . $q->url_hosting . '" target="_blank">'. $q->url_hosting .'</a>';
            })
            ->rawColumns(['url_hosting']) // Mengizinkan HTML di kolom url_hosting
            ->make(true);
        }

        $palanta = Desa::TemaPalanta();
        $natra = Desa::TemaNatra();
        $esensi = Desa::TemaEsensi();
        $tema = Desa::Tema();

        return view('laporan.tema', compact('fillters', 'palanta', 'natra', 'esensi', 'tema'));
    }
}
