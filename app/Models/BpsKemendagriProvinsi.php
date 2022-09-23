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
    protected $table = 'bps_kemendagri_provinsi';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];
}
