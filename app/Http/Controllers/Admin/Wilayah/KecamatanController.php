<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegionRequest;
use App\Models\Region;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class KecamatanController extends Controller
{
    public function index(Request $request)
    {
        $r = Region::with(['child'])->find(67);

        if ($request->ajax()) {
            return DataTables::of(Region::kecamatan())
                ->addIndexColumn()
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

    public function edit($id)
    {
        return view('admin.wilayah.kecamatan.edit', [
            'kecamatan' => Region::kecamatan()->findOrFail($id),
        ]);
    }

    public function update(RegionRequest $request, $id)
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
