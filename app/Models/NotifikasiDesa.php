<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiDesa extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'notifikasi_desa';

    /** {@inheritdoc} */
    protected $guarded = [];

    /**
     * Non-aktifkan notififikasi untuk desa, asumsi saat ini
     * setiap notifikasi hanya dikirim sekali.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param mixed $notif
     * @param mixed $desaId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNonAktifkan($query, $notif, $desaId)
    {
        // foreach ($notif as $data) {
        //     $query->updateOrCreate(
        //         ['id_notifikasi' => $data->id, 'id_desa' => $desaId],
        //         ['status' => 0, 'tgl_kirim' => now()]
        //     );
        // }

        $notif->each(function ($item) use ($query, $desaId) {
            $query->updateOrCreate(
                ['id_notifikasi' => $item->id, 'id_desa' => $desaId],
                ['status' => 0, 'tgl_kirim' => now()]
            );
        });
    }
}
