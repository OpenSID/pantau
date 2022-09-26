<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpsKemendagriKabupaten extends Model
{
    use HasFactory;

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'kode_kabupaten_kemendagri';

    /**
     * {@inheritdoc}
     */
    protected $keyType = 'string';

    /**
     * {@inheritdoc}
     */
    protected $table = 'bps_kemendagri_kabupaten';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * Define a one-to-many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function kecamatan()
    {
        return $this->hasMany(
            BpsKemendagriKecamatan::class,
            'kode_kabupaten_kemendagri',
        );
    }

    /**
     * Define a has-many-through relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function desa()
    {
        return $this->hasManyThrough(
            BpsKemendagriDesa::class,
            BpsKemendagriKecamatan::class,
            'kode_kabupaten_kemendagri',
            'kode_kecamatan_kemendagri',
        );
    }
}
