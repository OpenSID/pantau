<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PekerjaanPmi extends Model
{
    use HasFactory;

    protected $table = 'pekerjaan_pmi';

    protected $fillable = [
        'nama',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
