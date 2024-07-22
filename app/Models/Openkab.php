<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Openkab extends Model
{
    use HasFactory;

     /** {@inheritdoc} */
     protected $table = 'openkab';

     /** {@inheritdoc} */
    protected $primaryKey = 'kode_kab';

    /** {@inheritdoc} */
    protected $keyType = 'string';

    /** {@inheritdoc} */
    public $incrementing = false;

     protected $fillable = [
        'kode_kab',
        'nama_kab',
        'kode_prov',
        'nama_prov',
        'nama_aplikasi',
        'sebutan_kab',
        'url',
        'versi',
        'jumlah_desa',
        'jumlah_penduduk',
        'jumlah_keluarga',
        'jumlah_rtm',
        'jumlah_bantuan',
        'tgl_rekam',
    ];
}
