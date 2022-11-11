<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opendk extends Model
{
    use HasFactory;

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

  /**
     * Scope a query versi Opendk.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVersi($query, $fillters = []) {
        return $query->selectRaw('versi, count(versi) as jumlah')->groupBy(['versi']);
    }
}
