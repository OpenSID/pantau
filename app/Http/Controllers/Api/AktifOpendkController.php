<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Opendk;
use Illuminate\Http\Request;

class AktifOpendkController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {                 
        return [
            'aktif' => Opendk::active()->filterWilayah($request)->count() ?? 0,
            'desa_total' => Desa::desaValid()->filterWilayah($request)->count() ?? 0,
        ];
        
    }
}
