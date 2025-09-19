<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserRegionAccess extends Model
{
    use HasFactory;
    protected $table = 'user_region_access';
    protected $fillable = [
        'user_id',
        'kode_provinsi',
        'kode_kabupaten',
    ];

    /**
     * Get the kabupaten associated with the UserRegionAccess
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function kabupaten(): HasOne
    {
        return $this->hasOne(Wilayah::class, 'kode_kab', 'kode_kabupaten');
    }
}
