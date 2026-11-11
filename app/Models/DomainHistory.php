<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DomainHistory extends Model
{
    protected $fillable = [
        'desa_id',
        'kode_desa',
        'domain',
        'domain_normalized',
        'is_active',
        'source',
        'obtained_at',
        'note',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'obtained_at' => 'datetime',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }
}
