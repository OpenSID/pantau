<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanAplikasi extends Model
{
    use HasFactory;

     /** {@inheritdoc} */
     protected $table = 'pengaturan_aplikasi';

     /** {@inheritdoc} */
     protected $guarded = [];


     /** {@inheritdoc} */
     public $incrementing = true;
}
