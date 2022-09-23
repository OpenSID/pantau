<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BpsKemendagriKecamatan extends Model
{
    use HasFactory;

    /**
     * {@inheritdoc}
     */
    protected $table = 'bps_kemendagri_kecamatan';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];
}
