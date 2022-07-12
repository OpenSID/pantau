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
                ->make(true);
        }

        abort(404);
    }
}
