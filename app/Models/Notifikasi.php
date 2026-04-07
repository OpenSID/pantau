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
        return $query->where('aktif', 1)
            ->where(function ($q) use ($desaId) {
                $q->whereHas('notifikasiDesa', function ($q) use ($desaId) {
                    $q->where('id_desa', $desaId)
                        ->where('status', '!=', 0);
                })
                ->orWhereDoesntHave('notifikasiDesa', function ($q) use ($desaId) {
                    $q->where('id_desa', $desaId);
                });
            });
    }
}
