<?php

namespace App\Http\Controllers;

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
            return DataTables::of($this->desa->kabupatenOpenSID())
                ->addIndexColumn()
                ->make(true);
        }

        return view('opendk.versi' ,compact('fillters'));
    }
}
