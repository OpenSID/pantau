<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Suku extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'ethnic_groups';

    /** {@inheritdoc} */
    protected $fillable = [
        'tbl_region_id',
        'adat_id',
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
     * Get all of the marga for the Suku
     *
     * @return HasMany
     */
    public function marga(): HasMany
    {
        return $this->hasMany(Marga::class, 'ethnic_group_id', 'id');
    }

    public function wilayahAdat()
    {
        return $this->belongsTo(WilayahAdat::class, 'adat_id');
    }
}
