<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Openkab extends Model
{
    use HasFactory;

     /** {@inheritdoc} */
     protected $table = 'openkab';

     protected $fillable = [
        'kode_kab',
        'nama_kab',
    ];
}
