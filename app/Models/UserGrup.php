<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGrup extends Model
{
    use HasFactory;

    protected $table = 'user_grup';

    protected $fillable = [
        'nama',
    ];
}
