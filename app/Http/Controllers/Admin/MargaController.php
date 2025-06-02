<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MargaExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\MargaRequest;
use App\Imports\MargaImport;
use App\Models\Marga;
use App\Models\Suku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class MargaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->excel) {
            $paramDatatable = json_decode($request->get('params'), 1);
            $request->merge($paramDatatable);
        }

        if ($request->ajax() || $request->excel) {
            $provinsi = $request->kode_provinsi;
            $suku = $request->suku;
            $query = DataTables::of(Marga::with(['suku' => static fn($q) => $q->with('region')])
                    ->when($provinsi, static fn($q) => $q->whereHas('suku.region', static fn($q) => $q->where('region_code', $provinsi)))
                    ->when($suku, static fn($q) => $q->where('ethnic_group_id', $suku))
                );
            if ($request->excel) {
                $query->filtering();

                return Excel::download(new MargaExport($query->results()), 'Wilayah-Marga.xlsx');
            }

            return $query->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $edit = '<a href="'.route('marga.edit', $data->id).'" class="btn btn-sm btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></a>';
                    $delete = '<button data-href="'.route('marga.destroy', $data->id).'" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></button>';

                    return '<div class="btn btn-group">'.$edit.$delete.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $fillters = [
                'kode_provinsi' => [],
                'suku' => [],
            ];
        if($request->suku){
            $suku = Suku::with('region')->findOrFail($request->suku);
            $fillters = [
                'kode_provinsi' => ['id' => $suku->region->region_code, 'name' => $suku->region->region_name],
                'suku' => ['id' => $suku->id, 'name' => $suku->name],
            ];
        }

        return view('admin.marga.index', compact('fillters'));
    }

    public function create()
    {
        return view('admin.marga.create');
    }

    public function store(MargaRequest $request)
    {
        $input = $request->all();
        if (Marga::create($input)) {
            return redirect('marga')->with('success', 'Data berhasil disimpan');
        }

        return back()->with('error', 'Data gagal disimpan');
    }

    public function edit($id)
    {
        return view('admin.marga.edit', [
            'marga' => Marga::with(['suku' => static fn($q) => $q->with('region')])->findOrFail($id),
        ]);
    }

    public function update(MargaRequest $request, $id)
    {
        $input = $request->all();
        $marga = Marga::findOrFail($id);

        if ($marga->update($input)) {
            return redirect('marga')->with('success', 'Data berhasil diubah');
        }

        return back()->with('error', 'Data gagal diubah');
    }

    public function import()
    {
        return view('admin.marga.import');
    }

    public function prosesImport(Request $request)
    {
        try {
            Excel::import(new MargaImport, $request->file('file')->store('temp'));
        } catch (\Exception $e) {
            report($e);

            return back()->with('error', 'Data gagal diimport');
        }

        // Hapus folder temp ketika sudah selesai
        Storage::deleteDirectory('temp');

        return redirect('marga')->with('success', 'Data berhasil diimport');
    }

    public function contohImport()
    {
        $file = public_path('/assets/import/data_marga.xlsx');

        return response()->download($file);
    }

    public function destroy($id)
    {
        if (Marga::destroy($id)) {
            return redirect('marga')->with('success', 'Data berhasil dihapus');
        }

        return redirect('marga')->with('error', 'Data gagal dihapus');
    }
}
