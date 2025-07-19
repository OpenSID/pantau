<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Exports\WilayahProvinsiExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegionProvinsiRequest;
use App\Models\Region;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class ProvinsiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->excel) {
            $paramDatatable = json_decode($request->get('params'), 1);
            $request->merge($paramDatatable);
        }

        if ($request->ajax() || $request->excel) {
            $query = DataTables::of(Region::provinsi());
            if ($request->excel) {
                $query->filtering();

                return Excel::download(new WilayahProvinsiExport($query->results()), 'Wilayah-Provinsi.xlsx');
            }

            return $query->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $edit = '<a href="' . url('provinsi/' . $data->id . '/edit') . '" class="btn btn-sm btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></a>';
                    $delete = '<button data-href="' . url('provinsi/' . $data->id) . '" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></button>';

                    return '<div class="btn btn-group">' . $edit . $delete . '</div>';
                })
                ->editColumn('nama_provinsi', function ($data) {
                    return $data->nama_provinsi_baru ?? $data->nama_provinsi;
                })
                ->rawColumns(['action', 'nama_provinsi'])
                ->make(true);
        }

        return view('admin.wilayah.provinsi.index');
    }

    public function datatables(Request $request)
    {
        if ($request->excel) {
            $paramDatatable = json_decode($request->get('params'), 1);
            $request->merge($paramDatatable);
        }

        if ($request->ajax() || $request->excel) {
            $query = DataTables::of(Region::provinsi());
            if ($request->excel) {
                $query->filtering();

                return Excel::download(new WilayahProvinsiExport($query->results()), 'Wilayah-Provinsi.xlsx');
            }

            return $query->addIndexColumn()
                ->make(true);
        }

        abort(404);
    }

    public function create()
    {
        return view('admin.wilayah.provinsi.create');
    }

    public function store(RegionProvinsiRequest $request)
    {
        $input = $request->all();
        $input['parent_code'] = 0;

        if (Region::create($input)) {
            $inputchild = [
                'region_code' => $input['region_code'] . '.00',
                'parent_code' => $input['region_code'],
                'deleted_at' => date('Y-m-d H:i:s'),
            ];
            Region::create($inputchild);

            return redirect('provinsi')->with('success', 'Data berhasil disimpan');
        }

        return back()->with('error', 'Data gagal disimpan');
    }

    public function edit($id)
    {
        return view('admin.wilayah.provinsi.edit', [
            'provinsi' => Region::provinsi()->findOrFail($id),
        ]);
    }

    public function update(RegionProvinsiRequest $request, $id)
    {
        $input = $request->all();
        $provinsi = Region::provinsi()->find($id);

        if ($provinsi->nama_provinsi != $input['region_name']) {
            $input['new_region_name'] = $input['region_name'];

            unset($input['region_name']);
        }

        if ($provinsi->update($input)) {
            return redirect('provinsi')->with('success', 'Data berhasil diubah');
        }

        return back()->with('error', 'Data gagal diubah');
    }

    public function destroy($id)
    {
        // pastikan tidak ada region dengan parent_code provinsi ini
        $region = Region::with(['child'])->find($id);

        if ($region->child->count()) {
            return redirect('provinsi')->with('error', 'Data gagal dihapus karena data ini menjadi induk dari kabupaten ' . $region->child->pluck('region_name')->join(', '));
        }

        if (Region::destroy($id)) {
            return redirect('provinsi')->with('success', 'Data berhasil dihapus');
        }

        return redirect('provinsi')->with('error', 'Data gagal dihapus');
    }
}
