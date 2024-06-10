<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Illuminate\Http\Request;

class WebsiteDashboardController extends Controller
{
    /** @var Desa */
    protected $desa;

    public function __construct()
    {
        $this->desa = new Desa();
    }

    public function index(Request $request)
    {
        $fillters = [
            'kode_provinsi' => $request->kode_provinsi,
            'kode_kabupaten' => $request->kode_kabupaten,
            'kode_kecamatan' => $request->kode_kecamatan,            
        ];
        return view('website.dashboard', [
            'fillters' => $fillters,            
        ]);
    }

    public function chartUsage(Request $request){
        $period = $request->get('period');
        $period = explode(' - ','2024-06-10 - 2024-06-30');
        
        $result = [
            'labels' => ['1','2'],
            'datasets' => [['label' => 'fda', 'data' => [9,14]],['label' => 'f', 'data' => [9,14]]],
        ];

        return response()->json($result);             
    }
}
