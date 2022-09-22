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
    protected $guarded = [];
}
