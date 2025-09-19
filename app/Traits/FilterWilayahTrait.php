<?php
namespace App\Traits;
trait FilterWilayahTrait
{
    public function scopeFilterWilayah($query, $request)
    {
        if ($request->kode_provinsi) {
            $query->where('kode_provinsi', $request->kode_provinsi);
        }
        if ($request->kode_kabupaten) {
            $query->where('kode_kabupaten', $request->kode_kabupaten);
        }
        if ($request->kode_kecamatan) {
            $query->where('kode_kecamatan', $request->kode_kecamatan);
        }
        if ($request->kode_desa) {
            $query->where('kode_desa', $request->kode_desa);
        }
        return $query;
    }
}
