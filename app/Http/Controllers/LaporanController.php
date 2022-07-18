<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LaporanController extends Controller
{
    /** @var Desa */
    protected $desa;

    public function __construct()
    {
        $this->desa = new Desa();
    }

    public function desa(Request $request)
    {
        if ($request->ajax()) {
            $fillters = [
                'kode_provinsi'  => $request->kode_provinsi,
                'kode_kabupaten' => $request->kode_kabupaten,
                'kode_kecamatan' => $request->kode_kecamatan,
                'status'         => $request->status,
            ];

            return DataTables::of(Desa::latest()->laporan($fillters))
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $delete = '<button data-href="' . url('laporan/desa/' . $data->id) . '" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></button>';

                    return '<div class="btn btn-group">' . $delete . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('laporan.desa');
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
        if ($request->ajax()) {
            return DataTables::of($this->desa->kabupatenOpenSID())->addIndexColumn()->make(true);
        }

        return view('laporan.kabupaten');
    }

    public function versi(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of($this->desa->versiOpenSID())->addIndexColumn()->make(true);
        }

        return view('laporan.versi');
    }
}
