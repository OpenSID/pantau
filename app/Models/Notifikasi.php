<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    
    public static function getSemuaNotif($id_desa)
    {
        $semua_notif = [];
        foreach (Notifikasi::all() as $item) {
            $semua_notif[] = Notifikasi::select('notifikasi.*')
                ->join('notifikasi_desa', 'notifikasi_desa.id_notifikasi', '=', 'notifikasi.id')
                ->where([
                    'id_notifikasi'=>$item['id'],
                    'id_desa'=>$id_desa,
                ])
                ->where('status', '!=', 0)
                ->get();
        }

        return $semua_notif;
    }

    public static function nonAktifkan($notif, $id_desa)
    {
        foreach ($notif as $data) {
            NotifikasiDesa::updateOrInsert(
                [
                'id_notifikasi'=>$data['id'],
                'id_desa'=>$id_desa,
            ],
                [
                'status'=>0,
                'tgl_kirim'=>date("Y-m-d H:i:s")
            ]
            );
        }
    }
}
