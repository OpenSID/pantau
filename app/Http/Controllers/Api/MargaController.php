<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Marga;
use Illuminate\Http\Request;

class MargaController extends Controller
{
    public function index(Request $request)
    {
        $kodeSuku = $request->get('suku');
        $nameSuku = $request->get('name_suku');
        $search = $request->get('q');
        $marga = Marga::selectRaw('id, name')
            ->when($kodeSuku, static fn ($q) => $q->whereRelation('suku', 'ethnic_group_id', $kodeSuku))
            ->when($nameSuku, static fn ($q) => $q->whereRelation('suku', 'name', $nameSuku))
            ->when($search, static fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->paginate();

        return response()->json([
            'results' => $marga->items(),
            'pagination' => [
                'more' => $marga->currentPage() < $marga->lastPage(),
            ],
        ]);
    }
}
