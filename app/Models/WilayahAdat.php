<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WilayahAdat extends Model
{
    use HasFactory;

    protected $table = 'adats';

    /** {@inheritdoc} */
    protected $fillable = [
        'tbl_region_id',
        'name',
    ];

    /**
     * Get the region that owns the Suku.
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'tbl_region_id');
    }

    /**
     * Get all of the suku for the WilayahAdat
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suku(): HasMany
    {
        return $this->hasMany(Suku::class, 'adat_id', 'id');
    }
}
