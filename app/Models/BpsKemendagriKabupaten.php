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
    protected $table = 'bps_kemendagri_kabupaten';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;
}
