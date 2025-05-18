<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marga extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'clans';

    /** {@inheritdoc} */
    protected $fillable = [
        'ethnic_group_id',
        'name',
    ];

    /**
     * Get the region that owns the Suku.
     */
    public function suku()
    {
        return $this->belongsTo(Suku::class, 'ethnic_group_id');
    }
}
