<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function wilayah()
    {
        return $this->hasMany(Wilayah::class, 'kode_kab', 'kode_kab');
    }

    public function getNamaWilayahAttribute()
    {
        $sebutanKab = ($this->attributes['sebutan_kab'] == '' || $this->attributes['sebutan_kab'] == null) ? 'Kabupaten' : ucwords(strtolower($this->attributes['sebutan_kab']));
        $namaKab = ucwords(strtolower($this->attributes['nama_kab']));

        if (Str::contains($namaKab, $sebutanKab)) {
            return $namaKab;
        } else {
            return $sebutanKab . ' ' . $namaKab;
        }
    }
}
