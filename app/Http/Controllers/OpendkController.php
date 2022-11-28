<?php

namespace App\Http\Controllers;

use App\Models\Opendk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Yajra\DataTables\Facades\DataTables;

class OpendkController extends Controller
{
    public function __construct()
    {
        $this->kecamatan = new Opendk();
        Config::set('title', 'opendk');
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

        return view('opendk.versi', compact('fillters'));
    }

    public function kecamatan(Request $request)
    {
        $fillters = [
            'versi' => $request->versi,
        ];

        if ($request->ajax()) {
            return DataTables::of(Opendk::kecamatan($request)->get())
                ->addIndexColumn()
                ->make(true);
        }

        return view('opendk.kecamatan', compact('fillters'));
    }

    public function peta(Request $request)
    {
        if ($request->ajax()) {
            $geoJSONdata = Opendk::get()->map(function ($kec) {
                $kec->content = "
                    <h6 class='text-center'><b style='color:red'>{$kec->sebutan_wilayah} {$kec->nama_kecamatan}</b></h6>
                    <b><table width='100%'>
                        <tbody><tr>
                            <td>{$kec->sebutan_wilayah}</td><td> : {$kec->sebutan_wilayah} Batu Berapit</td>
                        </tr>

                        <tr>
                        <td>Kab/Kota</td><td> : {$kec->nama_kabupaten}</td>
                        </tr>
                        <tr>
                            <td>Provinsi</td><td> : {$kec->nama_provinsi}</td>
                        </tr>
                        <tr>
                            <td>Jumlah Desa</td><td> : {$kec->jumlah_desa}</td>
                        </tr>

                        <tr>
                            <td>Batas Wilayah</td><td> : {$kec->batas_wilayah}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td><td> : {$kec->alamat}</td>
                        </tr>
                        <tr>
                            <td>Website</td><td> : <a href='http://{$kec->url}' target='_blank'>{$kec->url}</a></td>
                        </tr>
                    </tbody></table></b>
                ";

                return $kec;
            });

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
