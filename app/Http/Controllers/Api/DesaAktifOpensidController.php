<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use Illuminate\Http\Request;

class DesaAktifOpensidController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $result = Desa::jumlahDesa()->filterWilayah($request)->first();

        return $result;
    }
}
