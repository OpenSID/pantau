<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suku extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'ethnic_groups';

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
}
