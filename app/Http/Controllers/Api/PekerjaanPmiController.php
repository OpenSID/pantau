<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PekerjaanPmi;
use Illuminate\Http\Request;

class PekerjaanPmiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q');
        $pekerjaanPmi = PekerjaanPmi::selectRaw('id, nama, nama as text')
            ->when($search, static fn($q) => $q->where('nama', 'like', "%{$search}%"))
            ->paginate();

        return response()->json([
            'results' => $pekerjaanPmi->items(),
            'pagination' => [
                'more' => $pekerjaanPmi->currentPage() < $pekerjaanPmi->lastPage(),
            ],
        ]);
    }
}
