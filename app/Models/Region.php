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
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['parent'];

    /**
     * Define a one-to-many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function parent()
    {
        return $this->hasMany(self::class, 'region_code', 'parent_code');
    }

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
        return $query->whereRaw('LENGTH(parent_code) = 2');
    }

    /**
     * Scope a query daftar kecamatan.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKecamatan($query)
    {
        return $query->whereRaw('LENGTH(parent_code) = 5');
    }

    /**
     * Scope a query daftar desa.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDesa($query)
    {
        return $query->whereRaw('LENGTH(parent_code) = 8');
    }
}
