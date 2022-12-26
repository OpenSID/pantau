<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackMobile extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'track_mobile';

    /** {@inheritdoc} */
    protected $keyType = 'string';

    /** {@inheritdoc} */
    protected $guarded = [];

    /** {@inheritdoc} */
    protected $primaryKey = 'id';

    /** {@inheritdoc} */
    public $incrementing = false;
}
