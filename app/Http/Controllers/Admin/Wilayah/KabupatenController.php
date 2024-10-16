<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Exports\WilayahKabupatenExport;
use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class KabupatenController extends Controller
{
    public function index()
    {
        return view('admin.wilayah.kabupaten.index');
    }

    public function datatables(Request $request)
    {
        if ($request->excel) {
            $paramDatatable = json_decode($request->get('params'), 1);
            $request->merge($paramDatatable);
        }

        if ($request->ajax() || $request->excel) {
            $query = DataTables::of(Region::kabupaten());
            if ($request->excel) {
                $query->filtering();

                return Excel::download(new WilayahKabupatenExport($query->results()), 'Wilayah-Kabupaten.xlsx');
            }

            return $query->addIndexColumn()
                    ->make(true);
        }

        abort(404);
    }
}
