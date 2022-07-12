<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Models\Region;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class KabupatenController extends Controller
{
    public function index()
    {
        return view('admin.wilayah.kabupaten.index');
    }

    public function datatables(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Region::kabupaten()->get())
                ->addIndexColumn()
                ->addColumn('kode_provinsi', static function ($row) {
                    return $row->parent->region_code;
                })
                ->addColumn('nama_provinsi', static function ($row) {
                    return $row->parent->region_name;
                })
                ->make(true);
        }

        abort(404);
    }
}
