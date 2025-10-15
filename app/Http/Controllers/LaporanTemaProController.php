<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LaporanTemaProController extends Controller
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
            $query = Desa::fillter($fillters);

            if ($tema) {
                $query->where('tema', 'like', "%{$tema}%");
            } else {
                // Filter hanya tema pro berdasarkan konstanta TEMA_PRO
                $query->where(function ($q) {
                    foreach (Desa::TEMA_PRO as $temaPro) {
                        $q->orWhere('tema', 'like', "%{$temaPro}%");
                    }
                })->whereNotNull('tema');
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('d/m/Y H:i') : '-';
                })
                ->editColumn('url_hosting', function ($row) {
                    return $row->url_hosting ? '<a href="'.$row->url_hosting.'" target="_blank">'.$row->url_hosting.'</a>' : '-';
                })
                ->rawColumns(['url_hosting'])
                ->make(true);
        }

        $temaPro = Desa::TemaPro();
        $temaProList = Desa::TemaProList();

        return view('laporan.tema-pro', compact('temaPro', 'temaProList'));
    }
}
