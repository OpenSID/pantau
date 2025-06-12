<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SukuExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\SukuRequest;
use App\Imports\SukuImport;
use App\Models\Region;
use App\Models\Suku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class SukuController extends Controller
{
    public function index(Request $request)
    {
        if ($request->excel) {
            $paramDatatable = json_decode($request->get('params'), 1);
            $request->merge($paramDatatable);
        }

        if ($request->ajax() || $request->excel) {
            $query = DataTables::of(Suku::with('region', 'wilayahAdat')->withCount('marga'));
            if ($request->excel) {
                $query->filtering();

                return Excel::download(new SukuExport($query->results()), 'Wilayah-Suku.xlsx');
            }

            return $query->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $detail = '<a href="'.route('marga.index', ['suku' => $data->id]).'" class="btn btn-sm btn-info btn-sm"><i class="fas fa-list-alt"></i></a>';
                    $edit = '<a href="'.route('suku.edit', $data->id).'" class="btn btn-sm btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></a>';
                    $delete = '<button data-href="'.route('suku.destroy', $data->id).'" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></button>';

                    return '<div class="btn btn-group">'.$detail.$edit.$delete.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.suku.index');
    }

    public function create()
    {
        return view('admin.suku.create');
    }

    public function store(SukuRequest $request)
    {
        $input = $request->all();
        $input['tbl_region_id'] = Region::where('region_code', $input['tbl_region_id'])->where('parent_code', 0)->first()->id;
        if (Suku::create($input)) {
            return redirect('suku')->with('success', 'Data berhasil disimpan');
        }

        return back()->with('error', 'Data gagal disimpan');
    }

    public function edit($id)
    {
        return view('admin.suku.edit', [
            'suku' => Suku::with('region')->findOrFail($id),
        ]);
    }

    public function update(SukuRequest $request, $id)
    {
        $input = $request->all();
        $input['tbl_region_id'] = Region::where('region_code', $input['tbl_region_id'])->where('parent_code', 0)->first()->id;
        $suku = Suku::findOrFail($id);

        if ($suku->update($input)) {
            return redirect('suku')->with('success', 'Data berhasil diubah');
        }

        return back()->with('error', 'Data gagal diubah');
    }

    public function import()
    {
        return view('admin.suku.import');
    }

    public function prosesImport(Request $request)
    {
        try {
            Excel::import(new SukuImport, $request->file('file')->store('temp'));
        } catch (\Exception $e) {
            report($e);

            return back()->with('error', 'Data gagal diimport');
        }

        // Hapus folder temp ketika sudah selesai
        Storage::deleteDirectory('temp');

        return redirect('suku')->with('success', 'Data berhasil diimport');
    }

    public function contohImport()
    {
        $file = public_path('/assets/import/data_suku.xlsx');

        return response()->download($file);
    }

    public function destroy($id)
    {
        if (Suku::destroy($id)) {
            return redirect('suku')->with('success', 'Data berhasil dihapus');
        }

        return redirect('suku')->with('error', 'Data gagal dihapus');
    }
}
