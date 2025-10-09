<?php

namespace App\Http\Controllers;

use App\Exports\DesaExport;
use App\Models\Desa;
use App\Models\Scopes\RegionAccessScope;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
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
        if ($request->excel) {
            $paramDatatable = json_decode($request->get('params'), 1);
            $request->merge($paramDatatable);
        }

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

        if ($request->ajax() || $request->excel) {
            $query = DataTables::of($this->desa->fillter($fillters)->laporan());
            if ($request->excel) {
                $query->filtering();

                return Excel::download(new DesaExport($query->results()), 'Desa-yang-memasang-OpenSID.xlsx');
            }

            return $query->addIndexColumn()                                
                ->rawColumns([])
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
            return DataTables::of($this->desa->newQueryWithoutScope(RegionAccessScope::class)->versiOpenSID($fillters))
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
