<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AdatExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdatRequest;
use App\Imports\SukuImport;
use App\Models\Region;
use App\Models\WilayahAdat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class AdatController extends Controller
{
    public function index(Request $request)
    {
        if ($request->excel) {
            $paramDatatable = json_decode($request->get('params'), 1);
            $request->merge($paramDatatable);
        }

        if ($request->ajax() || $request->excel) {
            $query = DataTables::of(WilayahAdat::with('region'));
            if ($request->excel) {
                $query->filtering();

                return Excel::download(new AdatExport($query->results()), 'Wilayah-WilayahAdat.xlsx');
            }

            return $query->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $edit = '<a href="'.route('adat.edit', $data->id).'" class="btn btn-sm btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></a>';
                    $delete = '<button data-href="'.route('adat.destroy', $data->id).'" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></button>';

                    return '<div class="btn btn-group">'.$edit.$delete.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.adat.index');
    }

    public function create()
    {
        return view('admin.adat.create');
    }

    public function store(AdatRequest $request)
    {
        $input = $request->all();
        $input['tbl_region_id'] = Region::where('region_code', $input['tbl_region_id'])->where('parent_code', 0)->first()->id;
        if (WilayahAdat::create($input)) {
            return redirect('adat')->with('success', 'Data berhasil disimpan');
        }

        return back()->with('error', 'Data gagal disimpan');
    }

    public function edit($id)
    {
        return view('admin.adat.edit', [
            'adat' => WilayahAdat::with('region')->findOrFail($id),
        ]);
    }

    public function update(AdatRequest $request, $id)
    {
        $input = $request->all();
        $input['tbl_region_id'] = Region::where('region_code', $input['tbl_region_id'])->where('parent_code', 0)->first()->id;
        $adat = WilayahAdat::findOrFail($id);

        if ($adat->update($input)) {
            return redirect('adat')->with('success', 'Data berhasil diubah');
        }

        return back()->with('error', 'Data gagal diubah');
    }

    public function import()
    {
        return view('admin.adat.import');
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

        return redirect('adat')->with('success', 'Data berhasil diimport');
    }

    public function contohImport()
    {
        $file = public_path('/assets/import/data_adat.xlsx');

        return response()->download($file);
    }

    public function destroy($id)
    {
        if (WilayahAdat::destroy($id)) {
            return redirect('adat')->with('success', 'Data berhasil dihapus');
        }

        return redirect('adat')->with('error', 'Data gagal dihapus');
    }
}
