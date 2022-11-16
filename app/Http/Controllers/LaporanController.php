<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LaporanController extends Controller
{
    /** @var Desa */
    protected $desa;

    public function __construct(Desa $desa)
    {
        $this->desa = $desa;
    }

    public function desa(Request $request)
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
            return DataTables::of($this->desa->fillter($fillters)->laporan())
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $delete = '<button data-href="'.url('laporan/desa/'.$data->id).'" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></button>';

                    return '<div class="btn btn-group">'.$delete.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('laporan.desa', compact('fillters'));
    }

    public function deleteDesa(Desa $desa)
    {
        if ($desa->delete()) {
            return redirect('laporan/desa')->with('success', 'Data berhasil dihapus');
        }

        return redirect('laporan/desa')->with('error', 'Data gagal dihapus');
    }

    public function kabupaten(Request $request)
    {
        $fillters = [
            'status' => $request->status,
        ];

        if ($request->ajax()) {
            return DataTables::of($this->desa->kabupatenOpenSID($fillters))
                ->addIndexColumn()
                ->make(true);
        }

        return view('laporan.kabupaten', compact('fillters'));
    }

    public function versi(Request $request)
    {
        $fillters = [
            'aktif' => $request->aktif,
        ];

        if ($request->ajax()) {
            return DataTables::of($this->desa->versiOpenSID($fillters))
                ->orderColumn('x.versi', function ($query, $order) {
                    $query
                        ->orderByRaw("cast(versi as signed) {$order}")
                        ->orderBy('versi', $order);
                })
                ->addIndexColumn()
                ->make(true);
        }

        return view('laporan.versi', compact('fillters'));
    }
}
