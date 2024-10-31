<?php

namespace App\Http\Controllers;

use App\Models\Pbb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Yajra\DataTables\Facades\DataTables;

class PbbController extends Controller
{
    private $pbb;

    protected $baseRoute = 'pbb';

    protected $baseView = 'pbb';

    public function __construct()
    {
        $this->pbb = new Pbb();
        Config::set('title', $this->baseView.'');
    }

    public function versi(Request $request)
    {
        $fillters = [
            'versi_pbb' => $request->versi_pbb,
        ];
        $listVersi = $this->getListVersion();
        if ($request->ajax()) {
            return DataTables::of(Pbb::wilayahkhusus()->versi($request)->get())
                ->addIndexColumn()
                ->make(true);
        }

        return view($this->baseView.'.versi', compact('fillters', 'listVersi'));
    }

    public function kecamatan(Request $request)
    {
        if ($request->excel) {
            $paramDatatable = json_decode($request->get('params'), 1);
            $request->merge($paramDatatable);
        }

        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'akses_opendk' => $request->akses_opendk,
            'versi_opendk' => $request->versi_opendk,
        ];

        $listVersi = $this->getListVersion();
        if ($request->ajax() || $request->excel) {
            $query = DataTables::of(Pbb::wilayahkhusus()->kecamatan($request)->selectRaw('updated_at as format_updated_at'));

            return $query->addIndexColumn()
            ->addColumn('action', function ($data) {
                $delete = '<button data-href="'.url('pbb/desa/'.$data->id).'" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></button>';

                return '<div class="btn btn-group">'.$delete.'</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view($this->baseView.'.kecamatan', compact('fillters', 'listVersi'));
    }

    public function kabupaten(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'akses_opendk' => $request->akses_opendk,
            'versi_opendk' => $request->versi_opendk,
        ];
        $listVersi = $this->getListVersion();
        if ($request->ajax()) {
            return DataTables::of(Pbb::wilayahkhusus()->with(['childKecamatan' => function ($r) {
                $r->select('kode_kabupaten', 'kode_kecamatan');
            }])->kabupaten($request)->get())
                ->addColumn('jumlah', function ($data) {
                    return $data->childKecamatan->count();
                })
                ->make(true);
        }

        return view($this->baseView.'.kabupaten', compact('fillters', 'listVersi'));
    }

    private function getListVersion()
    {
        return Pbb::selectRaw('DISTINCT right((LEFT(replace(versi, \'.\',\'\'),4)),4) as versi')->get()->sortByDesc('versi')->map(function ($item) {
            return $item->versi;
        })->values()->all();
    }

    public function deleteDesa(Pbb $desa)
    {
        if ($desa->delete()) {
            return redirect('pbb/kecamatan')->with('success', 'Data berhasil dihapus');
        }

        return redirect('pbb/kecamatan')->with('error', 'Data gagal dihapus');
    }
}
