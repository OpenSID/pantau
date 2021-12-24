<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\WilayahRequest;
use App\Models\Wilayah;

class WilayahController extends Controller
{
    /** @var Wilayah */
    protected $wilayah;

    public function __construct(Wilayah $wilayah)
    {
        $this->wilayah = $wilayah;
    }

    public function index(WilayahRequest $request)
    {
        return $this->wilayah
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->orWhere(function ($query) use ($request) {
                    $query
                        ->orWhere('nama_prov', 'like', "%{$request->search}%")
                        ->orWhere('nama_kab', 'like', "%{$request->search}%")
                        ->orWhere('nama_kec', 'like', "%{$request->search}%")
                        ->orWhere('nama_desa', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('kode_prov'), function ($query) use ($request) {
                $query->where('kode_prov', $request->kode_prov);
            })
            ->when($request->filled('kode_kab'), function ($query) use ($request) {
                $query->where('kode_kab', $request->kode_kab);
            })
            ->when($request->filled('kode_kec'), function ($query) use ($request) {
                $query->where('kode_kec', $request->kode_kec);
            })
            ->when($request->filled('kode_desa'), function ($query) use ($request) {
                $query->where('kode_desa', $request->kode_desa);
            })
            ->paginate($request->per_page)
            ->withQueryString();
    }

    public function provinsi(WilayahRequest $request)
    {
        return $this->wilayah->provinsi()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->orWhere('nama_prov', 'like', "%{$request->search}%");
            })
            ->when($request->filled('kode_prov'), function ($query) use ($request) {
                $query->where('kode_prov', $request->kode_prov);
            })
            ->paginate($request->per_page)
            ->withQueryString();
    }

    public function kabupaten(WilayahRequest $request)
    {
        return $this->wilayah->kabupaten()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->orWhere('nama_kab', 'like', "%{$request->search}%");
            })
            ->when($request->filled('kode_prov'), function ($query) use ($request) {
                $query->where('kode_prov', $request->kode_prov);
            })
            ->when($request->filled('kode_kab'), function ($query) use ($request) {
                $query->where('kode_kab', $request->kode_kab);
            })
            ->paginate($request->per_page)
            ->withQueryString();
    }

    public function kecamatan(WilayahRequest $request)
    {
        return $this->wilayah->kecamatan()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->orWhere('nama_kec', 'like', "%{$request->search}%");
            })
            ->when($request->filled('kode_kab'), function ($query) use ($request) {
                $query->where('kode_kab', $request->kode_kab);
            })
            ->when($request->filled('kode_kec'), function ($query) use ($request) {
                $query->where('kode_kec', $request->kode_kec);
            })
            ->paginate($request->per_page)
            ->withQueryString();
    }

    public function desa(WilayahRequest $request)
    {
        return $this->wilayah->desa()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->orWhere('nama_desa', 'like', "%{$request->search}%");
            })
            ->when($request->filled('kode_kec'), function ($query) use ($request) {
                $query->where('kode_kec', $request->kode_kec);
            })
            ->when($request->filled('kode_desa'), function ($query) use ($request) {
                $query->where('kode_desa', $request->kode_desa);
            })
            ->paginate($request->per_page)
            ->withQueryString();
    }
}
