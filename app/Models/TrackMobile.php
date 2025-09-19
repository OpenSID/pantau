<?php

namespace App\Models;

use Carbon\Carbon;
use Faker\Provider\Base;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrackMobile extends BaseModel
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
                $q->whereRaw('left(track_mobile.kode_desa, 2) = \''.$request['kode_provinsi'].'\'');
            });
        }
        if (isset($request['kode_kabupaten'])) {
            $query->when($request['kode_kabupaten'], function ($q) use ($request) {
                $q->whereRaw('left(track_mobile.kode_desa, 5) = \''.$request['kode_kabupaten'].'\'');
            });
        }

        if (isset($request['kode_kecamatan'])) {
            $query->when($request['kode_kecamatan'], function ($q) use ($request) {
                $q->whereRaw('left(track_mobile.kode_desa, 8) = \''.$request['kode_kecamatan'].'\'');
            });
        }

        if (isset($request['kode_desa'])) {
            $query->when($request['kode_desa'], function ($q) use ($request) {
                $q->whereRaw('track_mobile.kode_desa = \''.$request['kode_desa'].'\'');
            });
        }

        if (isset($request['akses_mobile'])) {
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

    public function scopeAktif($query, $batasTgl)
    {
        $maksimalTanggal = Carbon::parse($batasTgl)->subDays(self::ACTIVE_DAYS)->format('Y-m-d');

        return $query->where('tgl_akses', '>=', $maksimalTanggal);
    }

    public function scopeProvinsi($query, $provinsi)
    {
        return $query->whereRaw('left(kode_desa, 2) = \''.$provinsi.'\'');
    }

    public function scopeKabupaten($query, $kabupaten)
    {
        return $query->whereRaw('left(kode_desa, 5) = \''.$kabupaten.'\'');
    }

    public function scopeKecamatan($query, $kecamatan)
    {
        return $query->whereRaw('left(kode_desa, 8) = \''.$kecamatan.'\'');
    }
}
