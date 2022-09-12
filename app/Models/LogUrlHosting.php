<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogUrlHosting extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'log_url_hosting';

    protected $guarded = [];

    /** {@inheritdoc} */
    protected $fillable = [
        'url',
        'status',
    ];

}
