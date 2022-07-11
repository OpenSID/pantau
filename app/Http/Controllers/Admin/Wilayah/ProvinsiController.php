<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Models\User;
use App\Models\Region;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProvinsiRequest;

class ProvinsiController extends Controller
{
    public function index()
    {
        // $provinsi = Region::provinsi()->get();

        // dd($provinsi);

        return view('admin.wilayah.provinsi.index');
    }

    public function datatables(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Region::provinsi()->get())->addIndexColumn()->make(true);
        }

        abort(404);
    }
}
