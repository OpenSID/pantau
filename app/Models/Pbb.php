<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pbb extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'pbb';

    protected $fillable = [
        'kode_desa',
        'nama_desa',
        'kode_kecamatan',
        'nama_kecamatan',
        'kode_kabupaten',
        'nama_kabupaten',
        'kode_provinsi',
        'nama_provinsi',
        'versi',
    ];
}
