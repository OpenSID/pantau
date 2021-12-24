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
            ->selectRaw("if(versi_lokal is null, `tgl_rekam_hosting`, if(versi_hosting is null, `tgl_rekam_lokal`, least(tgl_rekam_lokal, tgl_rekam_hosting))) as tgl_rekam")
            ->whereRaw("(select if(versi_lokal is null, tgl_rekam_hosting, if(versi_hosting is null, tgl_rekam_lokal, least(tgl_rekam_lokal, tgl_rekam_hosting))) as tgl_rekam) >= date(now()) - interval 7 day")
            ->orderBy("tgl_rekam");
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
        return $query
            ->select(['nama_kabupaten', 'nama_provinsi'])
            ->selectRaw("group_concat(distinct versi_lokal order by versi_lokal) as versi_lokal")
            ->selectRaw("group_concat(distinct versi_hosting order by versi_hosting) as versi_hosting")
            ->selectRaw("(select count(*) from desa as x where x.nama_provinsi = desa.nama_provinsi and x.nama_kabupaten = desa.nama_kabupaten and x.versi_lokal <> '') as offline")
            ->selectRaw("(select count(*) from desa as x where x.nama_provinsi = desa.nama_provinsi and x.nama_kabupaten = desa.nama_kabupaten and x.versi_hosting <> '') as online")
            ->groupBy(['nama_kabupaten', 'nama_provinsi']);
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

    /**
     * Set ip lokal.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param array $value
     * @return void
     */
    public function setIpLokalAttribute(array $value)
    {
        if (is_local($value['url']) || is_local($value['ip_address'])) {
            $this->attributes['ip_lokal'] = $value['ip_address'];
        }
    }

    /**
     * Set ip hosting.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param array $value
     * @return void
     */
    public function setIpHostingAttribute(array $value)
    {
        if (!is_local($value['url']) || !is_local($value['ip_address'])) {
            $this->attributes['ip_hosting'] = $value['ip_address'];
        }
    }

    /**
     * Set versi lokal.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     *   'version'    => '21.12-premium-beta01',
     * ];
     * ```
     * @param array $value
     * @return void
     */
    public function setVersiLokalAttribute(array $value)
    {
        if (is_local($value['url']) || is_local($value['ip_address'])) {
            $this->attributes['versi_lokal'] = $value['version'];
        }
    }

    /**
     * Set versi hosting.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     *   'version'    => '21.12-premium-beta01',
     * ];
     * ```
     * @param array $value
     * @return void
     */
    public function setVersiHostingAttribute(array $value)
    {
        if (!is_local($value['url']) || !is_local($value['ip_address'])) {
            $this->attributes['versi_hosting'] = $value['version'];
        }
    }

    /**
     * Set url lokal.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param mixed $value
     * @return void
     */
    public function setUrlLokalAttribute($value)
    {
        if (is_local($value['url']) || is_local($value['ip_address'])) {
            $this->attributes['url_lokal'] = $value['url'];
        }
    }

    /**
     * Set url hosting.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param mixed $value
     * @return void
     */
    public function setUrlHostingAttribute($value)
    {
        if (!is_local($value['url']) || !is_local($value['ip_address'])) {
            $this->attributes['url_hosting'] = $value['url'];
        }
    }

    /**
     * Set tanggal rekam lokal.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param array $value
     * @return void
     */
    public function setTglRekamLokalAttribute(array $value)
    {
        if (is_local($value['url']) || is_local($value['ip_address'])) {
            $this->attributes['tgl_rekam_lokal'] = now();
        }
    }

    /**
     * Set tanggal rekam hosting.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param array $value
     * @return void
     */
    public function setTglRekamHostingAttribute(array $value)
    {
        if (!is_local($value['url']) || !is_local($value['ip_address'])) {
            $this->attributes['tgl_rekam_hosting'] = now();
        }
    }

    /**
     * Set tanggal akses lokal.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param array $value
     * @return void
     */
    public function setTglAksesLokalAttribute(array $value)
    {
        if (is_local($value['url']) || is_local($value['ip_address'])) {
            $this->attributes['tgl_akses_lokal'] = now();
        }
    }

    /**
     * Set tanggal akses hosting.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param array $value
     * @return void
     */
    public function setTglAksesHostingAttribute(array $value)
    {
        if (!is_local($value['url']) || !is_local($value['ip_address'])) {
            $this->attributes['tgl_akses_hosting'] = now();
        }
    }
}
