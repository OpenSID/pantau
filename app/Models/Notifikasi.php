<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'notifikasi';

    public static function get_semua_notif($id_desa)
    {
        $semua_notif = [];
        foreach(Notifikasi::all() as $item)
        {
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

    public static function non_aktifkan($notif, $id_desa)
    {
        foreach ($notif as $data)
        {
            NotifikasiDesa::updateOrInsert([
                'id_notifikasi'=>$data['id'],
                'id_desa'=>$id_desa,
            ],
            [
                'status'=>0,
                'tgl_kirim'=>date("Y-m-d H:i:s")
            ]);
        }
    }
}
