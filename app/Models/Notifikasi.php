<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notifikasi extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'notifikasi';

    /**
     * Scope semua notif dari desa.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param mixed $desaId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSemuaNotifDesa($query, $desaId)
    {
        return DB::select("select n.* from notifikasi as n where n.aktif = 1 and ((select nd.id from notifikasi_desa as nd where nd.id_notifikasi = n.id and nd.id_desa = '{$desaId}' and nd.status <> 0) is not null or (select nd.id from notifikasi_desa as nd where nd.id_notifikasi = n.id and nd.id_desa = '{$desaId}') is null)");
    }
}
