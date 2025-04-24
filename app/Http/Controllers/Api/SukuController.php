<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Suku;
use Illuminate\Http\Request;

class SukuController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'kode_prov' => 'required|integer',
        ]);

        $suku = Suku::select(['id','name'])->whereRelation('region', 'region_code', $request->get('kode_prov'))->paginate();

        return response()->json([
            'results' => $suku->items(),
            'pagination' => [
                'more' => $suku->currentPage() < $suku->lastPage(),
            ],
        ]);
    }
}
