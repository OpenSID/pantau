<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PekerjaanPmiExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\PekerjaanPmiRequest;
use App\Imports\PekerjaanPmiImport;
use App\Models\PekerjaanPmi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class PekerjaanPmiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->excel) {
            $paramDatatable = json_decode($request->get('params'), 1);
            $request->merge($paramDatatable);
        }

        if ($request->ajax() || $request->excel) {
            $query = DataTables::of(PekerjaanPmi::query());
            if ($request->excel) {
                $query->filtering();

                return Excel::download(new PekerjaanPmiExport($query->results()), 'Data-Pekerjaan-PMI.xlsx');
            }

            return $query->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $edit = '<a href="'.route('pekerjaan-pmi.edit', $data->id).'" class="btn btn-sm btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></a>';
                    $delete = '<button data-href="'.route('pekerjaan-pmi.destroy', $data->id).'" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></button>';

                    return '<div class="btn btn-group">'.$edit.$delete.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.pekerjaan-pmi.index');
    }

    public function create()
    {
        return view('admin.pekerjaan-pmi.create');
    }

    public function store(PekerjaanPmiRequest $request)
    {
        $input = $request->all();

        if (PekerjaanPmi::create($input)) {
            return redirect('pekerjaan-pmi')->with('success', 'Data berhasil disimpan');
        }

        return back()->with('error', 'Data gagal disimpan');
    }

    public function edit($id)
    {
        return view('admin.pekerjaan-pmi.edit', [
            'pekerjaanPmi' => PekerjaanPmi::findOrFail($id),
        ]);
    }

    public function update(PekerjaanPmiRequest $request, $id)
    {
        $input = $request->all();
        $pekerjaanPmi = PekerjaanPmi::findOrFail($id);

        if ($pekerjaanPmi->update($input)) {
            return redirect('pekerjaan-pmi')->with('success', 'Data berhasil diubah');
        }

        return back()->with('error', 'Data gagal diubah');
    }

    public function import()
    {
        return view('admin.pekerjaan-pmi.import');
    }

    public function prosesImport(Request $request)
    {
        try {
            Excel::import(new PekerjaanPmiImport, $request->file('file')->store('temp'));
        } catch (\Exception $e) {
            report($e);

            return back()->with('error', 'Data gagal diimport');
        }

        // Hapus folder temp ketika sudah selesai
        Storage::deleteDirectory('temp');

        return redirect('pekerjaan-pmi')->with('success', 'Data berhasil diimport');
    }

    public function contohImport()
    {
        $file = public_path('/assets/import/data_pekerjaan_pmi.xlsx');

        return response()->download($file);
    }

    public function destroy($id)
    {
        if (PekerjaanPmi::destroy($id)) {
            return redirect('pekerjaan-pmi')->with('success', 'Data berhasil dihapus');
        }

        return redirect('pekerjaan-pmi')->with('error', 'Data gagal dihapus');
    }
}
