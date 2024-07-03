<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogBackup extends Model
{
    use HasFactory;
    

    protected $table = 'log_backup';
    

    protected $fillable = [
        'status',
        'log',
    ];
    
}
