<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackMobile extends Model
{
    use HasFactory;

    const ACTIVE_DAYS = 7;

    /** {@inheritdoc} */
    protected $table = 'track_mobile';

    /** {@inheritdoc} */
    protected $keyType = 'string';

    /** {@inheritdoc} */
    protected $guarded = [];

    /** {@inheritdoc} */
    protected $primaryKey = 'id';

    /** {@inheritdoc} */
    public $incrementing = false;

    public function scopeWilayahKhusus($query)
    {
        $provinsi = session('provinsi');
        $query->when($provinsi, function ($r) use ($provinsi) {
            $r->whereIn('kode_desa', function ($s) use ($provinsi) {
                $s->select('kode_desa')->from('kode_wilayah')->where('kode_prov', $provinsi->kode_prov);
            });
        });
    }

    public function scopeDesa($query, $fillters = [])
    {
        return $query->filter($fillters)->distinct('kode_desa')->select(['kode_desa']);
    }

    public function desa()
    {
        return $this->hasOne(Desa::class, 'kode_desa', 'kode_desa');
    }

    protected function scopeFilter($query, $request)
    {
        if (isset($request['kode_provinsi'])) {
            $query->when($request['kode_provinsi'], function ($q) use ($request) {
                $q->whereRaw('left(kode_desa, 2) = \''.$request['kode_provinsi'].'\'');
            });
        }
        if (isset($request['kode_kabupaten'])) {
            $query->when($request['kode_kabupaten'], function ($q) use ($request) {
                $q->whereRaw('left(kode_desa, 5) = \''.$request['kode_kabupaten'].'\'');
            });
        }

        if (isset($request['kode_kecamatan'])) {
            $query->when($request['kode_kecamatan'], function ($q) use ($request) {
                $q->whereRaw('left(kode_desa, 8) = \''.$request['kode_kecamatan'].'\'');
            });
        }

        if (isset($request['kode_desa'])) {
            $query->when($request['kode_desa'], function ($q) use ($request) {
                $q->whereRaw('kode_desa = \''.$request['kode_desa'].'\'');
            });
        }

        if (isset($request['kode_kecamatan'])) {
            $query->when(! empty($request['akses_mobile']), function ($query) use ($request) {
                $interval = 'interval '.self::ACTIVE_DAYS.' day';
                $sign = '>=';
                switch($request['akses_mobile']) {
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
            });
        }

        return $query;
    }

    public function scopeActive($query)
    {
        return $query->whereRaw('tgl_akses >= now() - interval '.self::ACTIVE_DAYS.' day');
    }

    public function scopeNonActive($query)
    {
        return $query->whereRaw('tgl_akses <= now() - interval '.self::ACTIVE_DAYS.' day');
    }
}
