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

    public function kecamatan(Request $request)
    {

        $fillters = [
            'versi' => $request->versi
        ];

        if ($request->ajax()) {
            return DataTables::of(Opendk::kecamatan($request)->get())
                ->addIndexColumn()
                ->make(true);
        }

        return view('opendk.kecamatan' ,compact('fillters'));
    }

    public function peta(Request $request)
    {
        if ($request->ajax()) {
            $geoJSONdata = Opendk::get();


            return response()->json([
               $geoJSONdata,
            ]);
        }
    }
}
