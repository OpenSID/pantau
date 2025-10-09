<?php

namespace App\Http\Controllers;

use App\Exports\KecamatanExport;
use App\Models\Desa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class LaporanKecamatanController extends Controller
{
    /** @var Desa */
    protected $desa;

    public function __construct(Desa $desa)
    {
        $this->desa = $desa;
    }

    /**
     * Menampilkan laporan kecamatan
     */
    public function index(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'status' => $request->status,
        ];

        if ($request->ajax() || $request->excel) {
            $query = DataTables::of($this->desa->kecamatanOpenSID($fillters));
            if ($request->excel) {
                $query->filtering();

                return Excel::download(new KecamatanExport($query->results()), 'Kecamatan-yang-memasang-OpenSID.xlsx');
            }

            return $query->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $view = '<a href="'.route('laporan.kecamatan.detail', $data->kode_kecamatan).'" class="btn btn-sm btn-info" title="Lihat Detail"><i class="fas fa-eye"></i></a>';

                    return '<div class="btn-group">'.$view.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('laporan.kecamatan.index', compact('fillters'));
    }
}
