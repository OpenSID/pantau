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

    public static function get_pengaturan()
    {
        $aplikasi = self::get();

        $data['cloud_storage'] = $aplikasi->where('key', 'cloud_storage')->first()->value ?? '';
        $data['waktu_backup'] = $aplikasi->where('key', 'waktu_backup')->first()->value ?? '';
        $data['maksimal_backup'] = $aplikasi->where('key', 'maksimal_backup')->first()->value ?? '';
        $data['akhir_backup'] = $aplikasi->where('key', 'akhir_backup')->first()->value ?? '';

        return $data;
    }
}
