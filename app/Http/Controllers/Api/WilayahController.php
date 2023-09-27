<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Region;
use App\Models\Wilayah;
use Illuminate\Http\Request;

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

        $desa = $this->wilayah
            ->when(strlen($request->kode) == 8, function ($query) use ($request, $provinsi, $kabupaten, $kecamatan) {
                $query->listDesa($request, $provinsi, $kabupaten, $kecamatan);
            })
            ->when(strlen($request->kode) == 5, function ($query) use ($request, $provinsi, $kabupaten) {
                $query->listKecamatan($request, $provinsi, $kabupaten);
            })
            ->when(strlen($request->kode) == 2, function ($query) use ($request, $provinsi) {
                $query->listKabupaten($request, $provinsi);
            })
            ->unless($request->kode, function ($query) use ($request) {
                $query->listProvinsi($request);
            })
            ->paginate();

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
