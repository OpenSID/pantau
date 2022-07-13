<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        return $query
            ->select(
                'tbl_regions.id',
                'tbl_regions.region_code AS kode_provinsi',
                'tbl_regions.region_name AS nama_provinsi',
            )->where('parent_code', 0);
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
                    'tbl_regions.region_code AS kode_kabupaten',
                    'tbl_regions.region_name AS nama_kabupaten',
                    'prov.region_code AS kode_provinsi',
                    'prov.region_name AS nama_provinsi'
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
                    'tbl_regions.region_code AS kode_kecamatan',
                    'tbl_regions.region_name AS nama_kecamatan',
                    'kab.region_code AS kode_kabupaten',
                    'kab.region_name AS nama_kabupaten',
                    'prov.region_code AS kode_provinsi',
                    'prov.region_name AS nama_provinsi',
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
                    'tbl_regions.region_code AS kode_desa',
                    'tbl_regions.region_name AS nama_desa',
                    'kec.region_code AS kode_kecamatan',
                    'kec.region_name AS nama_kecamatan',
                    'kab.region_code AS kode_kabupaten',
                    'kab.region_name AS nama_kabupaten',
                    'prov.region_code AS kode_provinsi',
                    'prov.region_name AS nama_provinsi',
                )
                ->join('tbl_regions AS kec', 'tbl_regions.parent_code', '=', 'kec.region_code')
                ->join('tbl_regions AS kab', 'kec.parent_code', '=', 'kab.region_code')
                ->join('tbl_regions AS prov', 'kab.parent_code', '=', 'prov.region_code')
                ->whereRaw('LENGTH(tbl_regions.parent_code) = 8');
    }

    public static function boot()
    {
        $user_id = Auth::user()->id;

        parent::boot();
        static::creating(function ($model) use ($user_id) {
            $model->created_by = $user_id;
            $model->updated_by = $user_id;
        });
        static::updating(function ($model) use ($user_id) {
            $model->updated_by = $user_id;
        });
    }
}
