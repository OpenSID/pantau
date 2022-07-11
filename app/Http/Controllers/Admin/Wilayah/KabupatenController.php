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
        // $kab = Region::kabupaten()->count();

        // dd($kab);

        return view('admin.wilayah.kabupaten.index');
    }

    public function datatables(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Region::kabupaten()->get())->addIndexColumn()->make(true);
        }

        abort(404);
    }
}
