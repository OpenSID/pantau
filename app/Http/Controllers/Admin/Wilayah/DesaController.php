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
            return DataTables::of(Region::desa()->get())
                ->addIndexColumn()
                ->addColumn('kode_provinsi', static function ($row) {
                    return $row->parent->parent->parent->region_code;
                })
                ->addColumn('nama_provinsi', static function ($row) {
                    return $row->parent->parent->parent->region_name;
                })
                ->addColumn('kode_kabupaten', static function ($row) {
                    return $row->parent->parent->region_code;
                })
                ->addColumn('nama_kabupaten', static function ($row) {
                    return $row->parent->parent->region_name;
                })
                ->addColumn('kode_kecamatan', static function ($row) {
                    return $row->parent->region_code;
                })
                ->addColumn('nama_kecamatan', static function ($row) {
                    return $row->parent->region_name;
                })
                ->make(true);
        }

        abort(404);
    }
}
