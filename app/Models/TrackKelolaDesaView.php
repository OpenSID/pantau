<?php

namespace App\Models;

use App\Models\Traits\HasRegionAccess;
use App\Traits\FilterWilayahTrait;

class TrackKelolaDesaView extends BaseModel
{
    use HasRegionAccess, FilterWilayahTrait;

    const ACTIVE_DAYS = 7;

    /** {@inheritdoc} */
    protected $table = 'track_kelola_desa_view';

    /** {@inheritdoc} */
    protected $keyType = 'string';

    /** {@inheritdoc} */
    protected $primaryKey = 'id_device';

    /** {@inheritdoc} */
    public $incrementing = false;

    /**
     * Scope untuk desa aktif (berdasarkan tanggal akses)
     */
    public function scopeActive($query, $days = 7)
    {
        return $query->whereRaw('tgl_akses >= now() - interval ? day', [$days]);
    }

    /**
     * Scope untuk desa non-aktif
     */
    public function scopeNonActive($query, $days = 7)
    {
        return $query->whereRaw('tgl_akses <= now() - interval ? day', [$days]);
    }

    protected function scopeActivePeriod($query, $period)
    {
        $interval = 'interval '.self::ACTIVE_DAYS.' day';
        $sign = '>=';
        switch($period) {
            case '1':
                $interval = 'interval '.self::ACTIVE_DAYS.' day';
                break;
            case '2':
                $interval = 'interval 2 month';
                break;
            case '3':
                $interval = 'interval 2 month';
                $sign = '<=';
                break;
        }

        return $query->whereRaw('tgl_akses '.$sign.' now() - '.$interval);
    }
}
