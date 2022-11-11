<?php

namespace App\Http\Controllers;

use App\Models\Opendk;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OpendkController extends Controller
{
    public function versi(Request $request)
    {
        $fillters = [
            'aktif' => $request->aktif,
        ];
        if ($request->ajax()) {
            return DataTables::of(Opendk::versi()->get())
                ->addIndexColumn()
                ->make(true);
        }

        return view('opendk.versi' ,compact('fillters'));
    }
}
