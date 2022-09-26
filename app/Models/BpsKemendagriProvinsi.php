<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpsKemendagriProvinsi extends Model
{
    use HasFactory;

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'kode_provinsi_kemendagri';

    /**
     * {@inheritdoc}
     */
    protected $keyType = 'string';

    /**
     * {@inheritdoc}
     */
    protected $table = 'bps_kemendagri_provinsi';

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
    public function kabupaten()
    {
        return $this->hasMany(
            BpsKemendagriKabupaten::class,
            'kode_provinsi_kemendagri',
        );
    }

    /**
     * Define a has-many-through relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function kecamatan()
    {
        return $this->hasManyThrough(
            BpsKemendagriKecamatan::class,
            BpsKemendagriKabupaten::class,
            'kode_provinsi_kemendagri',
            'kode_kabupaten_kemendagri',
        );
    }
}
