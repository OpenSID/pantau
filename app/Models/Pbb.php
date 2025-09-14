<?php

namespace App\Models;

use App\Traits\FilterWilayahTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pbb extends Model
{
    use HasFactory, FilterWilayahTrait;

    const ACTIVE_DAYS = 7;

    /** {@inheritdoc} */
    protected $table = 'pbb';

    protected $fillable = [
        'kode_desa',
        'nama_desa',
        'kode_kecamatan',
        'nama_kecamatan',
        'kode_kabupaten',
        'nama_kabupaten',
        'kode_provinsi',
        'nama_provinsi',
        'versi',
    ];

    /**
     * Scope a query versi Opendk.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVersi($query, $fillters = [])
    {
        return $query->filterDatatable($fillters)->selectRaw('versi, count(versi) as jumlah, right((LEFT(replace(versi, \'.\',\'\'),4)),4) as versi_clean')->groupBy(['versi']);
    }

    /**
     * Scope a query Kecamatan Opendk.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKecamatan($query, $fillters = [])
    {
        return $query->filterDatatable($fillters)->select('*');
    }

    public function scopeKabupaten($query, $fillters = [])
    {
        return $query->filterDatatable($fillters)->distinct('kode_kabupaten, nama_kabupaten, nama_provinsi, kode_provinsi')->select(['kode_kabupaten', 'nama_kabupaten', 'nama_provinsi', 'kode_provinsi']);
    }

    public function childKecamatan()
    {
        return $this->hasMany(Pbb::class, 'kode_kabupaten', 'kode_kabupaten');
    }

    protected function scopeFilterDatatable($query, $fillters)
    {
        return $query->when(! empty($fillters['versi_opendk']), function ($query) use ($fillters) {
            return $query->whereRaw("right((LEFT(replace(versi, '.',''),4)),4) = '".$fillters['versi_opendk']."'");
        })->when(! empty($fillters['akses_opendk']), function ($query) use ($fillters) {
            $interval = 'interval '.self::ACTIVE_DAYS.' day';
            $sign = '>=';
            switch($fillters['akses_opendk']) {
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

            return $query->whereRaw('updated_at '.$sign.' now() - '.$interval);
        })->when(! (empty($fillters['kode_provinsi'])), function ($query) use ($fillters) {
            return $query->whereKodeProvinsi($fillters['kode_provinsi']);
        })->when(! (empty($fillters['kode_kabupaten'])), function ($query) use ($fillters) {
            return $query->whereKodeKabupaten($fillters['kode_kabupaten']);
        })->when(! (empty($fillters['kode_kecamatan'])), function ($query) use ($fillters) {
            return $query->whereKodeKecamatan($fillters['kode_kecamatan']);
        });
    }

    /**
     * Scope a query Kecamatan Opendk.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSummaryCount($query, $groupByColumn)
    {
        return $query->selectRaw("count($groupByColumn) as total")->groupBy($groupByColumn);
    }

    public function scopeActive($query)
    {
        return $query->whereRaw('updated_at >= now() - interval '.self::ACTIVE_DAYS.' day');
    }

    public function scopeNonActive($query)
    {
        return $query->whereRaw('updated_at <= now() - interval '.self::ACTIVE_DAYS.' day');
    }

    public function scopeWilayahKhusus($query)
    {
        $provinsi = session('provinsi');

        return $query
        ->when($provinsi, function ($query, $provinsi) {
            $query->where('kode_provinsi', $provinsi->kode_prov);
        });
    }

    /**
     * Scope a query jumlah Kecamatan.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVersiTerbaru($query, $version)
    {
        return $query->where('versi', 'like', "%{$version}%");
    }

    public function getFormatUpdatedAtAttribute()
    {
        if ($this->updated_at) {
            return $this->updated_at->format('Y-m-d H:i:s');
        }

        return null;
    }

    public function scopeAktif($query, $batasTgl)
    {
        $maksimalTanggal = Carbon::parse($batasTgl)->subDays(7)->format('Y-m-d');

        return $query->where('updated_at', '>=', $maksimalTanggal);
    }
}
