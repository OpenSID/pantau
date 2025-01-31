<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    public function desa()
    {
        return $this->hasMany(Desa::class, 'kode_kabupaten', 'kode_kab');
    }

    public function getNamaWilayahAttribute()
    {
        $sebutanKab = ($this->attributes['sebutan_kab'] == '' || $this->attributes['sebutan_kab'] == null) ? 'Kabupaten' : ucwords(strtolower($this->attributes['sebutan_kab']));
        $namaKab = ucwords(strtolower($this->attributes['nama_kab']));

        if (Str::contains($namaKab, $sebutanKab)) {
            return $namaKab;
        } else {
            return $sebutanKab.' '.$namaKab;
        }
    }

    public function scopeLatestVersion($query)
    {
        return $query->orderBy('versi', 'desc')->first()->versi ?? 'Belum ada data';
    }

    public function scopeJumlahProvinsi($query)
    {
        return $query->select('kode_prov')->distinct('kode_prov')->count();
    }

    public function scopeJumlahDesa($query)
    {
        return $query->sum('jumlah_desa');
    }

    public function latestDesaVersion()
    {
        return $this->desa()->latestVersion();
    }
}
