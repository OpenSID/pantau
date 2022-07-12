<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Models\Region;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class KecamatanController extends Controller
{
    public function index()
    {
        return view('admin.wilayah.kecamatan.index');
    }

    public function datatables(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Region::kecamatan()->get())
                ->addIndexColumn()
                ->addColumn('nama_provinsi', static function ($row) {
                    return $row->parent->parent->region_name;
                })
                ->addColumn('nama_kabupaten', static function ($row) {
                    return $row->parent->region_name;
                })
                ->make(true);
        }

        abort(404);
    }
}
