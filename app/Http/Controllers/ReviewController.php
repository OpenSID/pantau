<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ReviewController extends Controller
{
    public function desaBaru(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Desa::desaBaru())->addIndexColumn()->make(true);
        }

        return view('review.desa_baru');
    }

    public function nonAktif(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(Desa::reviewDesa())->addIndexColumn()->make(true);
        }

        return view('review.desa_nonaktif');
    }
}
