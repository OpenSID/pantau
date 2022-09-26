<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpsKemendagriDesa extends Model
{
    use HasFactory;

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = 'kode_desa_kemendagri';

    /**
     * {@inheritdoc}
     */
    protected $keyType = 'string';

    /**
     * {@inheritdoc}
     */
    protected $table = 'bps_kemendagri_desa';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;
}
