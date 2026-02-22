<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WilayahBoundaryResource;
use App\Models\WilayahBoundary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class WilayahBoundaryController extends Controller
{
    /**
     * Display a listing of boundaries.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'level' => 'sometimes|in:prov,kab,kec,kel',
            'kode' => 'sometimes|string|max:13',
            'search' => 'sometimes|string|max:100',
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        $query = WilayahBoundary::with('region');

        // Filter by level
        if (isset($validated['level'])) {
            $query->level($validated['level']);
        }

        // Filter by kode
        if (isset($validated['kode'])) {
            $query->where('kode', $validated['kode']);
        }

        // Search by region name
        if (isset($validated['search'])) {
            $query->whereHas('region', function ($q) use ($validated) {
                $q->where('region_name', 'like', "%{$validated['search']}%")
                  ->orWhere('region_code', 'like', "%{$validated['search']}%");
            });
        }

        $perPage = $validated['per_page'] ?? 20;
        $boundaries = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => WilayahBoundaryResource::collection($boundaries->items()),
            'meta' => [
                'current_page' => $boundaries->currentPage(),
                'per_page' => $boundaries->perPage(),
                'total' => $boundaries->total(),
                'last_page' => $boundaries->lastPage(),
            ],
        ]);
    }

    /**
     * Display the specified boundary.
     *
     * @param  string  $kode
     * @return JsonResponse
     */
    public function show(string $kode): JsonResponse
    {
        $boundary = WilayahBoundary::with('region')->find($kode);

        if (!$boundary) {
            return response()->json([
                'success' => false,
                'message' => 'Boundary not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new WilayahBoundaryResource($boundary),
        ]);
    }

    /**
     * Get boundaries as GeoJSON.
     *
     * @param  string  $level
     * @return JsonResponse
     */
    public function geojson(string $level): JsonResponse
    {
        if (!in_array($level, ['prov', 'kab', 'kec', 'kel'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid level. Must be one of: prov, kab, kec, kel',
            ], 400);
        }

        // Cache GeoJSON for 1 hour
        $cacheKey = "boundaries.geojson.{$level}";
        
        $geojson = Cache::remember($cacheKey, 3600, function () use ($level) {
            $boundaries = WilayahBoundary::level($level)
                ->with('region')
                ->get();

            $features = $boundaries->map(function ($boundary) {
                return $boundary->toGeoJSONFeature();
            })->filter(function ($feature) {
                // Filter out boundaries without path data
                return !empty($feature['geometry']['coordinates']);
            })->values();

            return [
                'type' => 'FeatureCollection',
                'features' => $features->toArray(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $geojson,
        ], 200, ['Content-Type' => 'application/geo+json']);
    }

    /**
     * Search boundaries by name or kode.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100',
            'level' => 'sometimes|in:prov,kab,kec,kel',
            'limit' => 'sometimes|integer|min:1|max:50',
        ]);

        $query = WilayahBoundary::with('region');

        // Filter by level if provided
        if (isset($validated['level'])) {
            $query->level($validated['level']);
        }

        // Search by kode or region name
        $searchTerm = $validated['q'];
        $query->where(function ($q) use ($searchTerm) {
            $q->where('kode', 'like', "%{$searchTerm}%")
              ->orWhereHas('region', function ($r) use ($searchTerm) {
                  $r->where('region_name', 'like', "%{$searchTerm}%")
                    ->orWhere('new_region_name', 'like', "%{$searchTerm}%");
              });
        });

        $limit = $validated['limit'] ?? 10;
        $boundaries = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => WilayahBoundaryResource::collection($boundaries),
        ]);
    }

    /**
     * Get boundary statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'provinsi' => DB::table('wilayah_boundaries')->where('level', 'prov')->count(),
            'kabupaten' => DB::table('wilayah_boundaries')->where('level', 'kab')->count(),
            'kecamatan' => DB::table('wilayah_boundaries')->where('level', 'kec')->count(),
            'kelurahan' => DB::table('wilayah_boundaries')->where('level', 'kel')->count(),
            'total' => DB::table('wilayah_boundaries')->count(),
            'last_updated' => DB::table('wilayah_boundaries')
                ->max('updated_at'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
