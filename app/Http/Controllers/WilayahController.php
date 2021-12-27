<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class WilayahController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Wilayah::query())->addIndexColumn()->make(true);
        }

        return view('wilayah.index');
    }
}
