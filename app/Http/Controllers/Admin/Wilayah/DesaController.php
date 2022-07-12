<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Models\Region;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class DesaController extends Controller
{
    public function index()
    {
        return view('admin.wilayah.desa.index');
    }

    public function datatables(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Region::desa())
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $edit = '<a href="' . url('desa/edit/' . $data->id) . '" class="btn btn-sm btn-warning btn-sm"><i class="fas fa-pencil-alt"></i></a>';
                    $delete = '<button data-href="' . url('desa/destroy/' . $data->id) . '" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="fas fa-trash"></i></button>';
                    return '<div class="btn btn-group">' . $edit . $delete . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        abort(404);
    }
}
