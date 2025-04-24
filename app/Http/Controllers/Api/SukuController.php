<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Suku;
use Illuminate\Http\Request;

class SukuController extends Controller
{
    public function index(Request $request)
    {
        $kodeProv = $request->get('kode_prov');
        $search = $request->get('q');
        $suku = Suku::select(['id','name'])
                ->when($kodeProv, static fn ($q) => $q->whereRelation('region', 'region_code', $kodeProv))
                ->when($search, static fn ($q) => $q->where('name', 'like', "%{$search}%"))
                ->paginate();

        return response()->json([
            'results' => $suku->items(),
            'pagination' => [
                'more' => $suku->currentPage() < $suku->lastPage(),
            ],
        ]);
    }
}
