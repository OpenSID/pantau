<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ReviewController extends Controller
{
    public function desaBaru(Request $request)
    {
        $fillters = [
            'akses' => $request->akses,
        ];

        if ($request->ajax()) {
            return DataTables::of(Desa::desaBaru($fillters))->addIndexColumn()->make(true);
        }

        return view('review.desa_baru', compact('fillters'));
    }

    public function nonAktif(Request $request)
    {
        $fillters = [
            'akses' => $request->akses,
        ];

        if ($request->ajax()) {
            return DataTables::of(Desa::reviewDesa($fillters))->addIndexColumn()->make(true);
        }

        return view('review.desa_nonaktif', compact('fillters'));
    }
}
