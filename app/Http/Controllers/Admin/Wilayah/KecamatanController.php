<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Exports\WilayahKecamatanExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegionKecamatanRequest;
use App\Models\Region;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class KecamatanController extends Controller
{
    public function index(Request $request)
    {
        $r = Region::with(['child'])->find(67);

        if ($request->excel) {
            $paramDatatable = json_decode($request->get('params'), 1);
            $request->merge($paramDatatable);
        }

        if ($request->ajax() || $request->excel) {
            $query = DataTables::of(Region::kecamatan());
            if ($request->excel) {
                $query->filtering();

                return Excel::download(new WilayahKecamatanExport($query->results()), 'Wilayah-Kecamatan.xlsx');
            }

            return $query->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $edit = '<a href="'.url('kecamatan/'.$data->id.'/edit').'" class="btn btn-sm btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></a>';
                    $delete = '<button data-href="'.url('kecamatan/'.$data->id).'" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></button>';

                    return '<div class="btn btn-group">'.$edit.$delete.'</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.wilayah.kecamatan.index');
    }

    public function create()
    {
        return view('admin.wilayah.kecamatan.create');
    }

    public function store(RegionKecamatanRequest $request)
    {
        $input = $request->all();

        if (Region::create($input)) {
            $inputchild = [
                'region_code' => $input['region_code'].'.0000',
                'parent_code' => $input['region_code'],
                'deleted_at' => date('Y-m-d H:i:s'),
            ];
            Region::create($inputchild);

            return redirect('kecamatan')->with('success', 'Data berhasil disimpan');
        }

        return back()->with('error', 'Data gagal disimpan');
    }

    public function edit($id)
    {
        return view('admin.wilayah.kecamatan.edit', [
            'kecamatan' => Region::kecamatan()->findOrFail($id),
        ]);
    }

    public function update(RegionKecamatanRequest $request, $id)
    {
        $input = $request->all();
        $kecamatan = Region::kecamatan()->find($id);

        if ($kecamatan->nama_kecamatan != $input['region_name']) {
            $input['new_region_name'] = $input['region_name'];

            unset($input['region_name']);
        }

        if ($kecamatan->update($input)) {
            return redirect('kecamatan')->with('success', 'Data berhasil diubah');
        }

        return back()->with('error', 'Data gagal diubah');
    }

    public function destroy($id)
    {
        // pastikan tidak ada region dengan parent_code kecamatan ini
        $region = Region::with(['child'])->find($id);

        if ($region->child->count()) {
            return redirect('kecamatan')->with('error', 'Data gagal dihapus karena data ini menjadi induk dari desa '.$region->child->pluck('region_name')->join(', '));
        }

        if (Region::destroy($id)) {
            return redirect('kecamatan')->with('success', 'Data berhasil dihapus');
        }

        return redirect('kecamatan')->with('error', 'Data gagal dihapus');
    }
}
