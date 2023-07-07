<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opendk extends Model
{
    use HasFactory;

    const ACTIVE_DAYS = 7;

    /** {@inheritdoc} */
    protected $table = 'opendk';

    /** {@inheritdoc} */
    protected $keyType = 'string';

    /** {@inheritdoc} */
    protected $guarded = [];

    /** {@inheritdoc} */
    protected $primaryKey = 'kode_kecamatan';

    /** {@inheritdoc} */
    public $incrementing = false;

    /** {@inheritdoc} */
    protected $casts = [
        'updated_at' => 'datetime',
        'tgl_rekam' => 'datetime',
        'batas_wilayah' => 'array',
    ];

    /**
     * Scope a query versi Opendk.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVersi($query, $fillters = [])
    {
        return $query->selectRaw('versi, count(versi) as jumlah')->groupBy(['versi']);
    }

    /**
     * Scope a query Kecamatan Opendk.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKecamatan($query, $fillters = [])
    {
        return $query->select('*')
        ->when($fillters['versi'] != null, function ($query) use ($fillters) {
            $query->where('versi', 'like', "%{$fillters['versi']}%");
        });
    }

    /**
     * Scope a query Kecamatan Opendk.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSummaryCount($query, $groupByColumn)
    {
        return $query->selectRaw("count($groupByColumn) as total")->groupBy($groupByColumn);
    }

    public function scopeActive($query)
    {
        return $query->whereRaw('updated_at >= now() - interval '.self::ACTIVE_DAYS.' day');
    }

    public function scopeNonActive($query)
    {
        return $query->whereRaw('updated_at <= now() - interval '.self::ACTIVE_DAYS.' day');
    }

    public function scopeWilayahKhusus($query)
    {
        $provinsi = session('provinsi');

        return $query
        ->when($provinsi, function ($query, $provinsi) {
            $query->where('kode_provinsi', $provinsi->kode_prov);
        });
    }

    /**
     * Scope a query jumlah Kecamatan.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVersiTerbaru($query, $version)
    {
        return $query->where('versi', 'like', "%{$version}%");
    }

    public function getFormatUpdatedAtAttribute()
    {
        if ($this->updated_at) {
            return $this->updated_at->format('Y-m-d H:i:s');
        }

        return null;
    }
}
