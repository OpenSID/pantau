<?php

namespace App\Http\Controllers\Admin\Wilayah;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WilayahKabupatenExport;

class KabupatenController extends Controller
{
    public function index()
    {
        return view('admin.wilayah.kabupaten.index');
    }

    public function datatables(Request $request)
    {
    if($request->excel){
        $paramDatatable = json_decode($request->get('params'), 1);            
        $request->merge($paramDatatable);            
    }

    if ($request->ajax() || $request->excel) {                        
        $query = DataTables::of(Region::kabupaten());
        if($request->excel){
            $query->filtering();
            return Excel::download(new WilayahKabupatenExport($query->results()), 'Wilayah-Kabupaten.xlsx');;
        }
        return $query->addIndexColumn()
                ->make(true);
        }

        abort(404);
    }
}
