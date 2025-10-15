<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'kabupaten';

    /** {@inheritdoc} */
    protected $fillable = [
        'nama_kabupaten',
        'kode_provinsi',
        'kode_kabupaten',
    ];

    /**
     * Scope for active kabupaten
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
