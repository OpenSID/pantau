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
            $query = $this->desa->query()
                ->select(['*'])
                ->selectRaw("greatest(coalesce(tgl_akses_lokal, 0), coalesce(tgl_akses_hosting, 0)) as tgl_akses");

            return DataTables::of($query)->addIndexColumn()->make(true);
        }

        return view('laporan.desa');
    }

    public function deleteDesa(Desa $desa)
    {
        $desa->delete();

        return redirect()->back()->with('alert-success', 'Data berhasil dihapus');
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
