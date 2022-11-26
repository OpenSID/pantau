<?php

namespace App\Http\Controllers;

use App\Models\Opendk;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OpendkController extends Controller
{
    public function __construct()
    {
        $this->kecamatan = new Opendk();
    }

    public function index()
    {
        return view('opendk.dashboard', [
            'total_semua' => $this->kecamatan->wilayahkhusus()->count(),
            'total_aktif' => $this->kecamatan->wilayahkhusus()->count(),
            'total_terbaru' => $this->kecamatan->wilayahkhusus()->versiterbaru()->count(),
            'daftar_baru' => $this->kecamatan->wilayahkhusus()->where('created_at', '>=', now()->subDay(7))->get(),
            // 'jumlahDesa' => $this->desa->jumlahDesa()->get()->first(),
            // 'desaBaru' => $this->desa->desaBaru()->count(),
            // 'kabupatenKosong' => collect($this->desa->kabupatenKosong())->count(),
        ]);
    }

    public function versi(Request $request)
    {
        $fillters = [
            'aktif' => $request->aktif,
        ];
        if ($request->ajax()) {
            return DataTables::of(Opendk::versi()->get())
                ->addIndexColumn()
                ->make(true);
        }

        return view('opendk.versi' ,compact('fillters'));
    }

    public function kecamatan(Request $request)
    {

        $fillters = [
            'versi' => $request->versi
        ];

        if ($request->ajax()) {
            return DataTables::of(Opendk::kecamatan($request)->get())
                ->addIndexColumn()
                ->make(true);
        }

        return view('opendk.kecamatan' ,compact('fillters'));
    }

    public function peta(Request $request)
    {
        if ($request->ajax()) {
            $geoJSONdata = Opendk::get();


            return response()->json(
               $geoJSONdata,
            );
        }
        return view('opendk.peta');
    }

    public function kabupatenKosong(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Opendk::kabupatenKosong()->get())
                ->addIndexColumn()
                ->make(true);
        }
    }
}
