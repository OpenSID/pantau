<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
     * @param  Builder  $query
     * @param mixed $notif
     * @param mixed $desaId
     * @return void
     */
    public function scopeNonAktifkan(Builder $query, $notif, $desaId)
    {
        if ($notif->isEmpty()) {
            return;
        }

        $now = now();
        $rows = $notif->map(fn ($item) => [
            'id_notifikasi' => $item->id,
            'id_desa' => $desaId,
            'status' => 0,
            'tgl_kirim' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        // Satu query untuk semua notifikasi, membutuhkan unique (id_notifikasi, id_desa)
        $query->upsert($rows, ['id_notifikasi', 'id_desa'], ['status', 'tgl_kirim', 'updated_at']);
    }
}
