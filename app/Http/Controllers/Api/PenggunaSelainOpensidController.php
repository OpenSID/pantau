<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Pbb;
use Illuminate\Http\Request;

class PenggunaSelainOpensidController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return [
            'pbb' => Pbb::filterWilayah($request)->count(),
            'anjungan' => Desa::filterWilayah($request)->anjungan()->count(),
        ];
    }
}
