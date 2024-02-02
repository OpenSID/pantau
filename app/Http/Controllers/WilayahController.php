<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WilayahController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(Wilayah::with('bpsKemendagriDesa'))
                ->addIndexColumn()
                ->make(true);
        }

        return view('wilayah.index');
    }
}
