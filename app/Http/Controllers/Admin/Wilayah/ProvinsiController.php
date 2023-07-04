<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProvinsiController extends Controller
{
    public function index()
    {
        return view('admin.wilayah.provinsi.index');
    }

    public function datatables(Request $request)
    {
        if ($request->ajax()) {
            $requestAll = $request->all();
            $requestAll['columns'][1]['name'] = 'region_code';
            $requestAll['columns'][2]['name'] = 'region_name';

            $request->merge($requestAll);
            return DataTables::of(Region::provinsi())
                ->addIndexColumn()
                ->make(true);
        }

        abort(404);
    }
}
