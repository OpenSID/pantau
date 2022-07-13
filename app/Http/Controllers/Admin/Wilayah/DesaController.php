<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Models\Region;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegionRequest;

class DesaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Region::desa())
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $edit   = '<a href="' . url('desa/edit/' . $data->id) . '" class="btn btn-sm btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></a>';
                    $delete = '<button data-href="' . url('desa/destroy/' . $data->id) . '" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></button>';

                    return '<div class="btn btn-group">' . $edit . $delete . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('admin.wilayah.desa.index');
    }

    public function create()
    {
        return view('admin.wilayah.desa.create');
    }

    public function store(RegionRequest $request)
    {
        $input = $request->all();

        $desa  = Region::create($input);

        return back()->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        return view('admin.wilayah.desa.edit', [
            'desa' => Region::desa()->findOrFail($id),
        ]);
    }

    public function update($id)
    {
    }

    public function destroy($id)
    {
        if (Region::desa()->destroy($id)) {
            return redirect()->with('alert-success', 'Data berhasil dihapus');
        }

        return back()->with('alert-error', 'Data gagal dihapus');
    }
}
