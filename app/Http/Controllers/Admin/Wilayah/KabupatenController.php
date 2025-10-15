<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Exports\WilayahKabupatenExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegionKabupatenRequest;
use App\Models\Region;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class KabupatenController extends Controller
{
    public function index(Request $request)
    {
        if ($request->excel) {
            $paramDatatable = json_decode($request->get('params'), 1);
            $request->merge($paramDatatable);
        }

        if ($request->ajax() || $request->excel) {
            $query = DataTables::of(Region::kabupaten());
            if ($request->excel) {
                $query->filtering();

                return Excel::download(new WilayahKabupatenExport($query->results()), 'Wilayah-Kabupaten.xlsx');
            }

            return $query->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $edit = '<a href="'.url('kabupaten/'.$data->id.'/edit').'" class="btn btn-sm btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></a>';
                    $delete = '<button data-href="'.url('kabupaten/'.$data->id).'" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></button>';

                    return '<div class="btn btn-group">'.$edit.$delete.'</div>';
                })
                ->editColumn('nama_kabupaten', function ($data) {
                    return $data->nama_kabupaten_baru ?? $data->nama_kabupaten;
                })
                ->rawColumns(['action', 'nama_kabupaten'])
                ->make(true);
        }

        return view('admin.wilayah.kabupaten.index');
    }

    public function datatables(Request $request)
    {
        if ($request->excel) {
            $paramDatatable = json_decode($request->get('params'), 1);
            $request->merge($paramDatatable);
        }

        if ($request->ajax() || $request->excel) {
            $query = DataTables::of(Region::kabupaten());
            if ($request->excel) {
                $query->filtering();

                return Excel::download(new WilayahKabupatenExport($query->results()), 'Wilayah-Kabupaten.xlsx');
            }

            return $query->addIndexColumn()
                ->make(true);
        }

        abort(404);
    }

    public function create()
    {
        return view('admin.wilayah.kabupaten.create');
    }

    public function store(RegionKabupatenRequest $request)
    {
        $input = $request->all();

        if (Region::create($input)) {
            $inputchild = [
                'region_code' => $input['region_code'].'.00',
                'parent_code' => $input['region_code'],
                'deleted_at' => date('Y-m-d H:i:s'),
            ];
            Region::create($inputchild);

            return redirect('kabupaten')->with('success', 'Data berhasil disimpan');
        }

        return back()->with('error', 'Data gagal disimpan');
    }

    public function edit($id)
    {
        return view('admin.wilayah.kabupaten.edit', [
            'kabupaten' => Region::kabupaten()->findOrFail($id),
        ]);
    }

    public function update(RegionKabupatenRequest $request, $id)
    {
        $input = $request->all();
        $kabupaten = Region::kabupaten()->find($id);

        if ($kabupaten->nama_kabupaten != $input['region_name']) {
            $input['new_region_name'] = $input['region_name'];

            unset($input['region_name']);
        }

        if ($kabupaten->update($input)) {
            return redirect('kabupaten')->with('success', 'Data berhasil diubah');
        }

        return back()->with('error', 'Data gagal diubah');
    }

    public function destroy($id)
    {
        // pastikan tidak ada region dengan parent_code kabupaten ini
        $region = Region::with(['child'])->find($id);

        if ($region->child->count()) {
            return redirect('kabupaten')->with('error', 'Data gagal dihapus karena data ini menjadi induk dari kecamatan '.$region->child->pluck('region_name')->join(', '));
        }

        if (Region::destroy($id)) {
            return redirect('kabupaten')->with('success', 'Data berhasil dihapus');
        }

        return redirect('kabupaten')->with('error', 'Data gagal dihapus');
    }
}
