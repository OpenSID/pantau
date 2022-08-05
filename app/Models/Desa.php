<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Desa extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'desa';

    /** {@inheritdoc} */
    protected $appends = ['format_created_at'];

    /** {@inheritdoc} */
    protected $casts = [
        'tgl_rekam_lokal' => 'datetime',
        'tgl_rekam_hosting' => 'datetime',
        'tgl_akses_lokal' => 'datetime',
        'tgl_akses_hosting' => 'datetime',
    ];

    /** {@inheritdoc} */
    protected $guarded = [];

    /**
     * Define a one-to-many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function akses()
    {
        return $this->hasMany(Akses::class);
    }

    /**
     * Define a many-to-many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function notifikasi()
    {
        return $this->belongsToMany(Notifikasi::class, 'notifikasi_desa', 'id_desa', 'id_notifikasi');
    }

    /**
     * Scope a query jumlah desa.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeJumlahDesa($query)
    {
        return $query
            ->selectRaw("count(id) as desa_total")
            ->selectRaw("(select count(id) from desa as x where x.versi_lokal <> '' and greatest(coalesce(x.tgl_akses_lokal, 0), coalesce(x.tgl_akses_hosting, 0)) >= now() - interval 7 day) desa_offline")
            ->selectRaw("(select count(id) from desa as x where x.versi_hosting <> '' and greatest(coalesce(x.tgl_akses_lokal, 0), coalesce(x.tgl_akses_hosting, 0)) >= now() - interval 7 day) desa_online")
            ->selectRaw("count(distinct nama_kabupaten) as kabupaten_total")
            ->selectRaw("(select count(distinct x.nama_kabupaten) from desa as x where x.versi_lokal <> '') kabupaten_offline")
            ->selectRaw("(select count(distinct x.nama_kabupaten) from desa as x where x.versi_hosting <> '') kabupaten_online")
            ->selectRaw("(select count(id) from desa as x where x.jenis = 2) bukan_desa")
            ->selectRaw("(select count(id) from desa as x where greatest(coalesce(x.tgl_akses_lokal, 0), coalesce(x.tgl_akses_hosting, 0)) < now() - interval 4 month) tidak_aktif")
            ->selectRaw("(select count(id) from desa as x where greatest(coalesce(x.tgl_akses_lokal, 0), coalesce(x.tgl_akses_hosting, 0)) >= now() - interval 7 day) aktif");
    }

    /**
     * Scope a query desa baru.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDesaBaru($query)
    {
        return $query
            ->select(['*'])
            ->selectRaw("(CASE WHEN (versi_hosting IS NULL) THEN versi_lokal WHEN (versi_lokal IS NULL) THEN versi_hosting WHEN (tgl_rekam_hosting > tgl_rekam_lokal) THEN versi_hosting ELSE versi_lokal END) as versi")
            ->where('created_at', '>=', now()->subDay(7));
    }

    /**
     * Scope a query review desa.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReviewDesa($query)
    {
        return $query
            ->select(['*'])
            ->selectRaw("date_format(greatest(coalesce(tgl_akses_lokal, 0), coalesce(tgl_akses_hosting, 0)),'%Y-%m-%d') as tgl_akses")
            ->whereRaw("greatest(coalesce(tgl_akses_lokal, 0), coalesce(tgl_akses_hosting, 0)) < now() - interval 4 month")
            ->where('jenis', 2);
    }

    /**
     * Scope a query kabupaten kosong.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKabupatenKosong($query)
    {
        return DB::select("select a.region_code, a.region_name as nama_kabupaten, c.region_name as nama_provinsi, b.jml_desa from (select region_code, region_name from `tbl_regions` t left join desa d on t.region_name = d.nama_kabupaten where length(region_code) = 5 and region_name not like 'kota %' and d.id is null ) a left join (select left(region_code, 5) as kabupaten_code, left(region_code, 2) as provinsi_code, count(*) as jml_desa from tbl_regions where char_length(region_code) = 13 group by kabupaten_code, provinsi_code ) b on a.region_code = b.kabupaten_code left join tbl_regions c on c.region_code = b.provinsi_code order by a.region_code");
    }

    /**
     * Scope a query kabupaten OpenSID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKabupatenOpenSID($query)
    {
        // return $query
        //     ->select(['nama_kabupaten', 'nama_provinsi'])
        //     ->selectRaw("(select count(*) from desa as x where x.nama_provinsi = desa.nama_provinsi and x.nama_kabupaten = desa.nama_kabupaten and x.versi_lokal <> '') as offline")
        //     ->selectRaw("(select count(*) from desa as x where x.nama_provinsi = desa.nama_provinsi and x.nama_kabupaten = desa.nama_kabupaten and x.versi_hosting <> '') as online")
        //     ->groupBy(['nama_kabupaten', 'nama_provinsi']);

        return $query
            ->selectRaw("sub.nama_kabupaten")
            ->selectRaw("sub.nama_provinsi")
            ->selectRaw("count(case when versi_lokal <> '' then 1 else null end) as 'offline'")
            ->selectRaw("count(case when versi_hosting <> '' then 1 else null end) as 'online'")
            ->fromSub(function ($query) {
                $query
                    ->select(
                        'd.versi_lokal',
                        'd.versi_hosting',
                        'desa.nama_kabupaten', 
                        'desa.nama_provinsi'
                    )
                    ->from('desa')
                    ->leftJoin('desa as d', 'desa.kode_desa', 'd.kode_desa');
            }, 'sub')
            ->groupBy(['sub.nama_kabupaten', 'sub.nama_provinsi']);
    }

    /**
     * Scope a query versi OpenSID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVersiOpenSID($query)
    {
        return DB::select("select * from (select versi, sum(case when jenis = 'offline' then 1 else 0 end) as offline, sum(case when jenis = 'online' then 1 else 0 end) as online from (select versi_lokal as versi, 'offline' as jenis from desa where versi_lokal <> '' union all select versi_hosting as versi, 'online' as jenis from desa where versi_hosting <> '' ) t group by versi ) as x");
    }

    /**
     * Set opensid valid attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setOpensidValidAttribute($value)
    {
        if (version_compare($value, '20.12', '>=')) {
            $this->attributes['opensid_valid'] = true;
        }
    }

    public static function hapusNonaktifTidakTerdaftar()
    {
        return DB::raw("DELETE FROM desa WHERE GREATEST(tgl_akses_lokal, tgl_akses_hosting) < NOW()-INTERVAL 4 MONTH AND jenis = 2");
    }

    /**
     * Scope a query desa map.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePeta($query)
    {
        return $query->whereRaw("CONCAT('',lat * 1) = lat") // tdk ikut sertakan data bukan bilangan
        ->whereRaw("CONCAT('',lng * 1) = lng") // tdk ikut sertakan data bukan bilangan
        ->whereRaw("lat BETWEEN -10 AND 6")
        ->whereRaw("lng BETWEEN 95 AND 142")
        ->whereRaw("GREATEST(tgl_akses_lokal, tgl_akses_hosting) >= NOW()-INTERVAL 60 DAY") //sejak dua bulan yang lalu
        ->where(function($query) {
            $query
            ->where('lat', '!=', config('tracksid.desa_contoh.lat'))
            ->where('lng', '!=', config('tracksid.desa_contoh.lng'));
            // ->where('kode_desa', '!=', config('tracksid.desa_contoh.kode_desa'));
        });
    }

    /**
     * Scope a query laporan desa.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLaporan($query)
    {
        return $query->select(['*'])->selectRaw("greatest(coalesce(tgl_akses_lokal, 0), coalesce(tgl_akses_hosting, 0)) as tgl_akses");
    }

    /**
     * Scope a query laporan desa.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFillter($query, array $fillters)
    {
        return $query->select(['*'])
            ->when($fillters['kode_provinsi'] ?? false, function ($query, $kode_provinsi) {
                $query->where('kode_provinsi', $kode_provinsi);
            })
            ->when($fillters['kode_kabupaten'] ?? false, function ($query, $kode_kabupaten) {
                $query->where('kode_kabupaten', $kode_kabupaten);
            })
            ->when($fillters['kode_kecamatan'] ?? false, function ($query, $kode_kecamatan) {
                $query->where('kode_kecamatan', $kode_kecamatan);
            })
            ->when($fillters['status'] == 1, function ($query) {
                $query->whereRaw("versi_hosting IS NOT NULL");
            })
            ->when($fillters['status'] == 2, function ($query) {
                $query->whereRaw("versi_lokal IS NOT NULL");
            })
            ->when($fillters['akses'], function ($query) use ($fillters) {
                switch ($fillters['akses']) {
                    // Sebelum dua bulan yang lalu
                    case '1':
                        $query->whereRaw("TIMESTAMPDIFF(MONTH, GREATEST(tgl_akses_lokal, tgl_akses_hosting), NOW()) > 1");
                        break;
                    // Sejak dua bulan yang lalu
                    case '2':
                        $query->whereRaw("TIMESTAMPDIFF(MONTH, GREATEST(tgl_akses_lokal, tgl_akses_hosting), NOW()) <= 1");
                        break;
                    // Sebelum empat bulan yang lalu
                    case '3':
                        $query->whereRaw("TIMESTAMPDIFF(MONTH, GREATEST(tgl_akses_lokal, tgl_akses_hosting), NOW()) > 3");
                        break;
                    // Sejak tujuh hari yang lalu
                    case '4':
                        $query->whereRaw("GREATEST(tgl_akses_lokal, tgl_akses_hosting) >= NOW()-INTERVAL 7 DAY");
                        break;
                    default:
                        break;
                }
            });
    }

    public function getFormatCreatedAtAttribute()
    {
        if ($this->created_at) {
            return $this->created_at->format('d/m/Y');
        }

        return null;
    }
}
