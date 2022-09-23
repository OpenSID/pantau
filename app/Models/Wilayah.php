<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Wilayah extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'kode_wilayah';

    /**
     * Define a one-to-one relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function bpsKemendagriDesa()
    {
        return $this->hasOne(BpsKemendagriDesa::class, 'kode_desa_kemendagri', 'kode_desa');
    }

    /**
     * Scope query provinsi.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeProvinsi($query)
    {
        return $query->select(['kode_prov', 'nama_prov'])->groupBy('kode_prov');
    }

    /**
     * Scope query list provinsi.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|$this
     */
    public function scopeListProvinsi($query, Request $request)
    {
        return $this->scopeProvinsi($query)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $query->where('nama_prov', 'like', "%{$request->cari}%");
            })
            ->orderBy('kode_prov', 'asc');
    }

    /**
     * Scope query kabupaten.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeKabupaten($query)
    {
        return $query->select(['kode_kab', 'nama_kab'])->groupBy('kode_kab');
    }

    /**
     * Scope query list kabupaten.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @param Request $request
     * @param mixed $provinsi
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|$this
     */
    public function scopeListKabupaten($query, Request $request, $provinsi = null)
    {
        return $query->select(['nama_prov', 'kode_prov', 'nama_kab', 'kode_kab'])
            ->where(function ($query) use ($provinsi) {
                $query->where('nama_prov', urldecode($provinsi))
                    ->orWhere('kode_prov', $provinsi);
            })
            ->when($request->filled('cari'), function ($query) use ($request) {
                $query->where('nama_kec', 'like', "%{$request->cari}%");
            })
            ->groupBy('kode_kab')
            ->orderBy('nama_kab', 'asc');
    }

    /**
     * Scope query kecamatan.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeKecamatan($query)
    {
        return $query->select(['kode_kec', 'nama_kec'])->groupBy('kode_kec');
    }

    /**
     * Scope query list kecamatan.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @param Request $request
     * @param mixed $provinsi
     * @param mixed $kabupaten
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|$this
     */
    public function scopeListKecamatan($query, Request $request, $provinsi = null, $kabupaten = null)
    {
        return $query->select(['nama_prov', 'kode_prov', 'nama_kab', 'kode_kab', 'nama_kec', 'kode_kec'])
            ->where(function ($query) use ($provinsi) {
                $query->where('nama_prov', urldecode($provinsi))
                    ->orWhere('kode_prov', $provinsi);
            })
            ->where(function ($query) use ($kabupaten) {
                $query->where('nama_kab', urldecode($kabupaten))
                    ->orWhere('kode_kab', $kabupaten);
            })
            ->when($request->filled('cari'), function ($query) use ($request) {
                $query->where('nama_kec', 'like', "%{$request->cari}%");
            })
            ->groupBy('kode_kec')
            ->orderBy('nama_kec', 'asc');
    }

    /**
     * Scope query desa.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|$this
     */
    public function scopeDesa($query)
    {
        return $query->select(['kode_desa', 'nama_desa'])->groupBy('kode_desa');
    }

    /**
     * Scope query list desa.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @param Request $request
     * @param mixed $provinsi
     * @param mixed $kabupaten
     * @param mixed $kecamatan
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|$this
     */
    public function scopeListDesa($query, Request $request, $provinsi = null, $kabupaten = null, $kecamatan = null)
    {
        return $query->select(['nama_prov', 'kode_prov', 'nama_kab', 'kode_kab', 'nama_kec', 'kode_kec', 'nama_desa', 'kode_desa'])
            ->where(function ($query) use ($provinsi) {
                $query->where('nama_prov', urldecode($provinsi))
                    ->orWhere('kode_prov', $provinsi);
            })
            ->where(function ($query) use ($kabupaten) {
                $query->where('nama_kab', urldecode($kabupaten))
                    ->orWhere('kode_kab', $kabupaten);
            })
            ->where(function ($query) use ($kecamatan) {
                $query->where('nama_kec', urldecode($kecamatan))
                    ->orWhere('kode_kec', $kecamatan);
            })
            ->when($request->filled('cari'), function ($query) use ($request) {
                $query->where('nama_desa', 'like', "%{$request->cari}%");
            })
            ->groupBy('kode_desa')
            ->orderBy('nama_desa', 'asc');
    }
}
