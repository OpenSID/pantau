<?php

namespace App\Http\Controllers;

use App\Enums\Layanan;
use App\Exports\DesaExport;
use App\Models\Desa;
use App\Models\Scopes\RegionAccessScope;
use App\Services\SebutanDesaService;
use App\Services\TemaService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class LaporanController extends Controller
{
    /** @var Desa */
    protected $desa;

    /** @var SebutanDesaService */
    protected $sebutanDesaService;

    public function __construct(Desa $desa, SebutanDesaService $sebutanDesaService)
    {
        $this->desa = $desa;
        $this->sebutanDesaService = $sebutanDesaService;
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
            'tipe_pengguna' => $request->tipe_pengguna,
            'layanan' => $request->layanan,
            'sebutan_desa' => $request->sebutan_desa,
            'tema' => $request->tema
        ];
        $hiddenColumns = [];
        $adminWilayah = auth()->check() && auth()->user()->isAdminWilayah();
        if ($adminWilayah) {
            $hiddenColumns[] = 'aksi';
            $hiddenColumns[] = 'kontak';
        }

        if ($request->ajax() || $request->excel) {
            $query = DataTables::of($this->desa->fillter($fillters)->laporan());
            if ($request->excel) {
                $query->filtering();
                if (in_array('aksi', $hiddenColumns)) {
                    unset($hiddenColumns['aksi']);
                }
                return Excel::download(new DesaExport($query->results(), $hiddenColumns), 'Desa-yang-memasang-OpenSID.xlsx');
            }

            return $query->addIndexColumn()
                ->editColumn('kontak', function ($q) {
                    $identitas = $q->kontak;
                    if ($identitas) {
                        // Escape output untuk mencegah XSS
                        $nama = e($identitas['nama'] ?? '-');
                        $hp = e($identitas['hp'] ?? '-');
                        return '<div><div>' . $nama . '</div><div>' . $hp . '</div></div>';                        
                    }

                    return '';
                })
                ->addColumn('action', function ($data) {
                    $delete = '<button data-href="' . url('laporan/desa/' . $data->id) . '" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></button>';

                    return '<div class="btn btn-group">' . $delete . '</div>';
                })->editColumn('layanan', function ($data) {
                    return (Layanan::tryFrom($data->layanan))?->label() ?? '-';
                })->rawColumns(['action', 'kontak'])
                ->make(true);
        }
        $sebutanDesaList = (new SebutanDesaService())->getSebutanDesaList();
        $temaList = (new TemaService())->getList();        
        return view('laporan.desa', compact('fillters', 'hiddenColumns', 'sebutanDesaList', 'temaList'));
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
