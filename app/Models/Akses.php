<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akses extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'akses';

    /** {@inheritdoc} */
    protected $fillable = ['desa_id', 'url_referrer', 'request_uri', 'client_ip', 'external_ip', 'opensid_version', 'tgl'];

    /** {@inheritdoc} */
    protected $cast = [
        'tgl' => 'datetime',
    ];

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public static function bersihkan()
    {
        $daftar_desa = self::latest('tgl')->limit(Desa::count())->get(['id', 'desa_id'])->unique('desa_id');

        // self::whereNotIn('id', array_column($daftar_desa, 'id'))->delete();

        foreach ($daftar_desa as $value) {
            self::where('desa_id', $value->desa_id)->where('id', '!=', $value->id)->limit(1000)->delete();
        }
    }
}
