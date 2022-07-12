<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'tbl_regions';

    /** {@inheritdoc} */
    protected $fillable = [
        'region_code',
        'region_name',
        'parent_code',
        'desa_id',
    ];

    /**
     * Scope a query daftar provinsi.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProvinsi($query)
    {
        return $query->where('parent_code', 0);
    }

    /**
     * Scope a query daftar kabupaten.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKabupaten($query)
    {
        return $query
                ->select(
                    'tbl_regions.id',
                    'tbl_regions.region_code AS kode',
                    'tbl_regions.region_name AS kabupaten',
                    'prov.region_name AS provinsi'
                )
                ->leftJoin('tbl_regions AS prov', 'tbl_regions.parent_code', '=', 'prov.region_code')
                ->whereRaw('LENGTH(tbl_regions.parent_code) = 2');
    }

    /**
     * Scope a query daftar kecamatan.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKecamatan($query)
    {
        return $query
                ->select(
                    'tbl_regions.id',
                    'tbl_regions.region_code AS kode',
                    'tbl_regions.region_name AS kecamatan',
                    'kab.region_name AS kabupaten',
                    'prov.region_name AS provinsi',
                )
                ->join('tbl_regions AS kab', 'tbl_regions.parent_code', '=', 'kab.region_code')
                ->join('tbl_regions AS prov', 'kab.parent_code', '=', 'prov.region_code')
                ->whereRaw('LENGTH(tbl_regions.parent_code) = 5');
    }

    /**
     * Scope a query daftar desa.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDesa($query)
    {
        return $query
                ->select(
                    'tbl_regions.id',
                    'tbl_regions.region_code AS kode',
                    'tbl_regions.region_name AS desa',
                    'kec.region_name AS kecamatan',
                    'kab.region_name AS kabupaten',
                    'prov.region_name AS provinsi',
                )
                ->join('tbl_regions AS kec', 'tbl_regions.parent_code', '=', 'kec.region_code')
                ->join('tbl_regions AS kab', 'kec.parent_code', '=', 'kab.region_code')
                ->join('tbl_regions AS prov', 'kab.parent_code', '=', 'prov.region_code')
                ->whereRaw('LENGTH(tbl_regions.parent_code) = 8');
    }
}
