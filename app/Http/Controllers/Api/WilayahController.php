<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Region;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WilayahController extends Controller
{
    /** @var Desa */
    protected $desa;

    /** @var Wilayah */
    protected $wilayah;

    public function __construct(Desa $desa, Wilayah $wilayah)
    {
        $this->desa = $desa;
        $this->wilayah = $wilayah;
    }

    public function desa(Request $request)
    {
        $this->validate($request, [
            'id_desa' => 'required|integer',
        ]);

        $desa = $this->desa->findOrFail($request->id_desa);

        return response()->json(['KODE_WILAYAH' => [$desa]]);
    }

    public function cariDesa(Request $request)
    {
        $desa = $this->wilayah
            ->select('*')
            ->selectRaw("concat(nama_desa, ' - ', nama_kec, ' - ', nama_kab, ' - ', nama_prov) as text")
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->orWhere(function ($query) use ($request) {
                    $query
                        ->orWhere('nama_desa', 'like', "%{$request->q}%")
                        ->orWhere('nama_kec', 'like', "%{$request->q}%");
                });
            })
            ->when(strlen($request->kode) == 5, function ($query) use ($request) {
                $query->where('kode_kab', substr($request->kode, 0, 5));
            })
            ->paginate();

        return response()->json([
            'results' => $desa->items(),
            'pagination' => [
                'more' => $desa->currentPage() < $desa->lastPage(),
            ],
        ]);
    }

    public function cariKabupaten(Request $request)
    {
        $desa = $this->wilayah
            ->select('*')
            ->selectRaw("concat(nama_desa, ' - ', nama_kec, ' - ', nama_kab, ' - ', nama_prov) as text")
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->orWhere(function ($query) use ($request) {
                    $query
                        ->orWhere('nama_kab', 'like', "%{$request->q}%")
                        ->orWhere('nama_prov', 'like', "%{$request->q}%");
                });
            })
            ->groupBy('kode_kab')
            ->paginate();

        return response()->json([
            'results' => $desa->items(),
            'pagination' => [
                'more' => $desa->currentPage() < $desa->lastPage(),
            ],
        ]);
    }

    public function ambilDesa(Request $request)
    {
        $this->validate($request, [
            'id_desa' => 'required|integer',
        ]);

        $desa = $this->wilayah->with('bpsKemendagriDesa')->findOrFail($request->id_desa);

        return response()->json(['KODE_WILAYAH' => [$desa]]);
    }

    public function kodeDesa(Request $request)
    {
        $this->validate($request, [
            'kode' => 'required',
        ]);

        $desa = $this->wilayah
            ->select(['kode_prov', 'nama_prov', 'kode_kab', 'nama_kab', 'kode_kec', 'nama_kec', 'kode_desa', 'nama_desa'])
            ->with('bpsKemendagriDesa')
            ->where('kode_desa', kode_wilayah($request->kode))
            ->firstOrFail();

        return response()->json($desa);
    }

    public function listWilayah(Request $request)
    {
        $this->validate($request, [
            'kode' => 'sometimes',
        ]);

        $provinsi = substr($request->kode, 0, 2);
        $kabupaten = substr($request->kode, 0, 5);
        $kecamatan = $request->kode;

        // For provinces, kabupaten, and kecamatan, use Region model (tbl_regions)
        // For desa, use Wilayah model (kode_wilayah)

        $kodeDesa = strlen($request->kode);

        if ($kodeDesa == 8) {
            // List desa - use Wilayah model
            $desa = $this->wilayah->listDesa($request, $provinsi, $kabupaten, $kecamatan)->paginate();
        } elseif ($kodeDesa == 5) {
            // List kecamatan - use Region model
            $query = Region::select(
                'tbl_regions.id',
                'tbl_regions.region_code as kode_kec',
                DB::raw('COALESCE(tbl_regions.new_region_name, tbl_regions.region_name) as nama_kec'),
                'kab.region_code as kode_kab',
                'kab.region_name as nama_kab',
                'prov.region_code as kode_prov',
                'prov.region_name as nama_prov'
            )
                ->join('tbl_regions as kab', 'tbl_regions.parent_code', '=', 'kab.region_code')
                ->join('tbl_regions as prov', 'kab.parent_code', '=', 'prov.region_code')
                ->where('kab.region_code', $kabupaten)
                ->whereRaw('LENGTH(tbl_regions.parent_code) = 5')
                ->when($request->filled('cari'), function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('tbl_regions.region_name', 'like', "%{$request->cari}%")
                          ->orWhere('tbl_regions.new_region_name', 'like', "%{$request->cari}%");
                    });
                })
                ->orderBy('nama_kec', 'asc');

            $desa = $query->paginate();
        } elseif ($kodeDesa == 2) {
            // List kabupaten - use Region model
            $query = Region::select(
                'tbl_regions.id',
                'tbl_regions.region_code as kode_kab',
                DB::raw('COALESCE(tbl_regions.new_region_name, tbl_regions.region_name) as nama_kab'),
                'prov.region_code as kode_prov',
                'prov.region_name as nama_prov'
            )
                ->leftJoin('tbl_regions as prov', 'tbl_regions.parent_code', '=', 'prov.region_code')
                ->where('prov.region_code', $provinsi)
                ->whereRaw('LENGTH(tbl_regions.parent_code) = 2')
                ->when($request->filled('cari'), function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('tbl_regions.region_name', 'like', "%{$request->cari}%")
                          ->orWhere('tbl_regions.new_region_name', 'like', "%{$request->cari}%");
                    });
                })
                ->orderBy('nama_kab', 'asc');

            $desa = $query->paginate();
        } else {
            // List provinsi - use Region model
            $query = Region::select(
                'id',
                'region_code as kode_prov',
                DB::raw('COALESCE(new_region_name, region_name) as nama_prov')
            )
                ->where('parent_code', 0)
                ->when($request->filled('cari'), function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('region_name', 'like', "%{$request->cari}%")
                          ->orWhere('new_region_name', 'like', "%{$request->cari}%");
                    });
                })
                ->orderBy('nama_prov', 'asc');

            $desa = $query->paginate();
        }

        return response()->json([
            'results' => $desa->items(),
            'pagination' => [
                'more' => $desa->currentPage() < $desa->lastPage(),
            ],
        ]);
    }

    public function kodeKecamatan(Request $request)
    {
        $this->validate($request, [
            'kode' => 'required',
        ]);

        $desa = $this->wilayah
            ->select(['kode_prov', 'nama_prov', 'kode_kab', 'nama_kab', 'kode_kec', 'nama_kec'])
            ->where('kode_kec', kode_kecamatan($request->kode))
            ->groupBy('kode_kec')
            ->firstOrFail();
        $desa->kode_desa = '';
        $desa->nama_desa = '';

        return response()->json($desa);
    }

    public function kabupatenDesa(Request $request)
    {
        return response()->json($this->wilayah->kabupatenDesa($request)->get());
    }

    public function regionData(Request $request)
    {
        $lastSync = $request->input('last_sync');
        $regions = Region::withTrashed()
            ->when($request->has('last_sync'), function ($query) use ($lastSync) {
                return $query->where('updated_at', '>', $lastSync);
            })
            ->get();

        return response()->json($regions);
    }
}
