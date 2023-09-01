<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        $states = '';
        $version = lastrelease_opensid();

        if ($provinsi = session('provinsi')) {
            $states = "and x.kode_provinsi={$provinsi->kode_prov}";
        }

        return $query
            ->selectRaw('count(id) as desa_total')
            ->selectRaw("(select count(id) from desa as x where x.versi_lokal <> '' and x.versi_hosting is null and coalesce(x.tgl_akses_lokal, 0) >= now() - interval 7 day {$states}) desa_offline")
            ->selectRaw("(select count(id) from desa as x where x.versi_hosting <> '' and greatest(coalesce(x.tgl_akses_lokal, 0), coalesce(x.tgl_akses_hosting, 0)) >= now() - interval 7 day {$states}) desa_online")
            ->selectRaw('count(distinct kode_kabupaten) as kabupaten_total')
            ->selectRaw("(select count(distinct x.kode_kabupaten) from desa as x where (x.versi_hosting like '{$version}-premium%' or x.versi_lokal like '{$version}-premium%') {$states} ) as kabupaten_premium")
            ->selectRaw("(select count(distinct x.kode_kabupaten) from desa as x where x.versi_lokal <> '' {$states}) kabupaten_offline")
            ->selectRaw("(select count(distinct x.kode_kabupaten) from desa as x where x.versi_hosting <> '' {$states}) kabupaten_online")
            ->selectRaw("(select count(id) from desa as x where x.jenis = 2 {$states}) bukan_desa")
            ->selectRaw("(select count(id) from desa as x where greatest(coalesce(x.tgl_akses_lokal, 0), coalesce(x.tgl_akses_hosting, 0)) < now() - interval 4 month {$states}) tidak_aktif")
            ->selectRaw("(select count(id) from desa as x where greatest(coalesce(x.tgl_akses_lokal, 0), coalesce(x.tgl_akses_hosting, 0)) >= now() - interval 7 day {$states}) aktif")
            ->when($provinsi, function ($query, $provinsi) {
                $query->where('kode_provinsi', $provinsi->kode_prov);
            });
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
            ->selectRaw('(CASE WHEN (versi_hosting IS NULL) THEN versi_lokal WHEN (versi_lokal IS NULL) THEN versi_hosting WHEN (tgl_rekam_hosting > tgl_rekam_lokal) THEN versi_hosting ELSE versi_lokal END) as versi')
            ->where('created_at', '>=', now()->subDay(7))
            // filter ip lokal
            ->whereRaw("(CASE WHEN ((url_hosting = '' || url_hosting IS NULL) && (url_lokal Like 'localhost%' || url_lokal Like '10.%' || url_lokal Like '127.%' || url_lokal Like '192.168.%' || url_lokal Like '169.254.%' || url_lokal REGEXP '(^172\.1[6-9]\.)|(^172\.2[0-9]\.)|(^172\.3[0-1]\.)')) THEN 0 ELSE 1 END) = 1") // 0 = i local
            ->when(session('provinsi'), function ($query, $provinsi) {
                $query->where('kode_provinsi', $provinsi->kode_prov);
            });
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
            ->whereRaw('greatest(coalesce(tgl_akses_lokal, 0), coalesce(tgl_akses_hosting, 0)) < now() - interval 4 month')
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
    public function scopeKabupatenOpenSID($query, $fillters = [])
    {
        // return $query
        //     ->select(['nama_kabupaten', 'nama_provinsi'])
        //     ->selectRaw("(select count(*) from desa as x where x.nama_provinsi = desa.nama_provinsi and x.nama_kabupaten = desa.nama_kabupaten and x.versi_lokal <> '') as offline")
        //     ->selectRaw("(select count(*) from desa as x where x.nama_provinsi = desa.nama_provinsi and x.nama_kabupaten = desa.nama_kabupaten and x.versi_hosting <> '') as online")
        //     ->groupBy(['nama_kabupaten', 'nama_provinsi']);

        return $query
            ->selectRaw('sub.kode_kabupaten')
            ->selectRaw('sub.nama_kabupaten')
            ->selectRaw('sub.kode_provinsi')
            ->selectRaw('sub.nama_provinsi')
            ->selectRaw("count(case when versi_lokal <> '' then 1 else null end) as 'offline'")
            ->selectRaw("count(case when versi_hosting <> '' then 1 else null end) as 'online'")
            ->fromSub(function ($query) use ($fillters) {
                $query
                    ->select(
                        'd.versi_lokal',
                        'd.versi_hosting',
                        'desa.kode_kabupaten',
                        'desa.nama_kabupaten',
                        'desa.kode_provinsi',
                        'desa.nama_provinsi'
                    )
                    ->from('desa')
                    ->leftJoin('desa as d', 'desa.kode_desa', 'd.kode_desa')
                    ->whereRaw("desa.kode_kabupaten <> ''")
                    // filter
                    ->when($fillters['status'] == 1, function ($query) {
                        $query->whereRaw('d.versi_hosting is not null');
                    })
                    ->when($fillters['status'] == 2, function ($query) {
                        $query->whereRaw('d.versi_lokal is not null');
                    })
                    ->when($fillters['status'] == 3, function ($query) {
                        $version = lastrelease_opensid();
                        $query->where('d.versi_hosting', 'like', "{$version}-premium%")
                                ->orWhere('d.versi_lokal', 'like', "{$version}-premium%");
                    });
            }, 'sub')
            ->when(session('provinsi'), function ($query, $provinsi) {
                $query->where('sub.kode_provinsi', $provinsi->kode_prov);
            })
            ->groupBy(['sub.nama_kabupaten', 'sub.nama_provinsi']);
    }

    /**
     * Scope a query versi OpenSID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVersiOpenSID($query, $fillters = [])
    {
        return $query->fromSub(function ($query) use ($fillters) {
            $query->selectRaw("versi, sum( CASE WHEN jenis = 'offline' THEN 1 ELSE 0 END ) AS offline, sum( CASE WHEN jenis = 'online' THEN 1 ELSE 0 END ) AS online")
                ->fromSub(function ($query) use ($fillters) {
                    $query
                        ->selectRaw("versi_lokal AS versi, 'offline' AS jenis ")
                        ->where('versi_lokal', '<>', '')
                        ->when(session('provinsi'), function ($query, $provinsi) {
                            $query->where('kode_provinsi', $provinsi->kode_prov);
                        })
                        ->when($fillters['aktif'] == '1', function ($query) {
                            $query->whereRaw('coalesce(tgl_akses_lokal, 0) >= now() - interval 7 day');
                        })
                        ->when($fillters['aktif'] == '0', function ($query) {
                            $query->whereRaw('coalesce(tgl_akses_lokal, 0) <= now() - interval 7 day');
                        })
                        ->unionAll(function ($query) use ($fillters) {
                            $query->selectRaw("versi_hosting AS versi, 'online' AS jenis ")
                                ->where('versi_hosting', '<>', '')
                                ->when(session('provinsi'), function ($query, $provinsi) {
                                    $query->where('kode_provinsi', $provinsi->kode_prov);
                                })
                                ->when($fillters['aktif'] == '1', function ($query) {
                                    $query->whereRaw('coalesce(tgl_akses_hosting, 0) >= now() - interval 7 day');
                                })
                                ->when($fillters['aktif'] == '0', function ($query) {
                                    $query->whereRaw('coalesce(tgl_akses_hosting, 0) <= now() - interval 7 day');
                                })
                                ->from('desa');
                        })
                        ->from('desa');
                }, 't')->groupBy(['versi']);
        }, 'x');
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
        return DB::raw('DELETE FROM desa WHERE GREATEST(COALESCE(tgl_akses_lokal,0), COALESCE(tgl_akses_hosting,0)) < NOW()-INTERVAL 1 MONTH');
    }

    /**
     * Scope a query desa map.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePeta($query)
    {
        return $query
            ->when(session('provinsi'), function ($query, $provinsi) {
                $query->where('kode_provinsi', $provinsi->kode_prov);
            })
            ->whereRaw("CONCAT('',lat * 1) = lat") // tdk ikut sertakan data bukan bilangan
            ->whereRaw("CONCAT('',lng * 1) = lng") // tdk ikut sertakan data bukan bilangan
            ->whereRaw('lat BETWEEN -10 AND 6')
            ->whereRaw('lng BETWEEN 95 AND 142')
            ->whereRaw('GREATEST(tgl_akses_lokal, tgl_akses_hosting) >= NOW()-INTERVAL 60 DAY') //sejak dua bulan yang lalu
            ->where(function ($query) {
                $query
                ->where('lat', '!=', config('tracksid.desa_contoh.lat'))
                ->where('lng', '!=', config('tracksid.desa_contoh.lng'));
            })
            ->orderBy('kode_desa', 'ASC');
    }

    /**
     * Scope a query laporan desa.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLaporan($query)
    {
        return $query
            // ->select(['*'])
            ->select(['nama_desa', 'kode_desa', 'nama_kecamatan', 'nama_kabupaten', 'kode_kecamatan', 'kode_kabupaten', 'nama_provinsi', 'kode_provinsi', 'versi_lokal', 'versi_hosting', 'jml_surat_tte', 'modul_tte', 'jml_penduduk', 'jml_artikel', 'jml_surat_keluar', 'jml_bantuan', 'jml_mandiri', 'jml_pengguna', 'jml_unsur_peta', 'jml_persil', 'jml_dokumen', 'jml_keluarga'])
            ->selectRaw('greatest(coalesce(tgl_akses_lokal, 0), coalesce(tgl_akses_hosting, 0)) as tgl_akses')
            ->when(auth()->check() == true, function ($query) {
                $query->selectRaw('url_lokal, url_hosting');
            })
            ->when(session('provinsi'), function ($query, $provinsi) {
                $query->where('kode_provinsi', $provinsi->kode_prov);
            });
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
                $query->whereRaw('versi_hosting IS NOT NULL');
            })
            ->when($fillters['status'] == 2, function ($query) {
                $query->whereRaw('versi_lokal IS NOT NULL');
            })
            ->when($fillters['status'] == 3, function ($query) {
                $query->where(function ($query_versi) {
                    $version = lastrelease_opensid();
                    $query_versi->where('versi_hosting', 'LIKE', $version.'-premium%')
                    ->orWhere('versi_lokal', 'LIKE', $version.'-premium%');
                });
            })
            ->when($fillters['akses'] == 1, function ($query) {
                $query->whereRaw('timestampdiff(month, greatest(coalesce(tgl_akses_lokal, 0), coalesce(tgl_akses_hosting, 0)), now()) > 1');
            })
            ->when($fillters['akses'] == 2, function ($query) {
                $query->whereRaw('timestampdiff(month, greatest(coalesce(tgl_akses_lokal, 0), coalesce(tgl_akses_hosting, 0)), now()) <= 1');
            })
            ->when($fillters['akses'] == 3, function ($query) {
                $query->whereRaw('timestampdiff(month, greatest(coalesce(tgl_akses_lokal, 0), coalesce(tgl_akses_hosting, 0)), now()) > 3');
            })
            ->when($fillters['akses'] == 4, function ($query) {
                $query->whereRaw('greatest(coalesce(tgl_akses_lokal, 0), coalesce(tgl_akses_hosting, 0)) >= now() - interval 7 day');
            })
            ->when($fillters['akses'] == 5, function ($query) {
                $query->whereRaw("versi_lokal <> '' and versi_hosting is null and coalesce(tgl_akses_lokal, 0) >= now() - interval 7 day");
            })
            ->when($fillters['versi_lokal'], function ($query, $versi) {
                $query->where('versi_lokal', $versi);
            })
            ->when($fillters['versi_hosting'], function ($query, $versi) {
                $query->where('versi_hosting', $versi);
            })
            ->when(in_array($fillters['tte'], ['1', '0']), function ($query) use ($fillters) {
                $query->where('modul_tte', $fillters['tte']);
            });
    }

    public function getFormatCreatedAtAttribute()
    {
        if ($this->created_at) {
            return $this->created_at->format('d/m/Y');
        }

        return null;
    }

    /**
     * Get all of the mobile for the Desa
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mobile()
    {
        return $this->hasMany(TrackMobile::class, 'kode_desa', 'kode_desa');
    }

    public function scopeWilayahKhusus($query)
    {
        $provinsi = session('provinsi');
        $query->when($provinsi, function ($r) use ($provinsi) {
            $r->where('kode_provinsi', $provinsi);
        });
    }
}
