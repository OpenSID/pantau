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
    public $timestamps = false;

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
        $keep = Desa::where('desa_id', $desa->id)
            ->orderBy('tgl', 'ASC')
            ->latest()
            ->take(1)
            ->pluck('id');

        Desa::whereNotIn('desa_id', $keep)
            ->delete();
    }
}
