<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opendk extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'opendk';

    /** {@inheritdoc} */
    protected $keyType = 'string';

    /** {@inheritdoc} */
    protected $guarded = [];

    /** {@inheritdoc} */
    protected $primaryKey = 'kode_kecamatan';

    /** {@inheritdoc} */
    public $incrementing = false;

    /** {@inheritdoc} */
    protected $casts = [
        'updated_at' => 'datetime',
        'tgl_rekam' => 'datetime',
    ];

    /**
     * Scope a query versi Opendk.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVersi($query, $fillters = []) {
        return $query->selectRaw('versi, count(versi) as jumlah')->groupBy(['versi']);
    }

    /**
     * Scope a query Kecamatan Opendk.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKecamatan($query, $fillters = []) {
        return $query->select('*')
        ->when($fillters['versi'] != null, function ($query) use ($fillters) {
            $query->where('versi', 'like', "%{$fillters['versi']}%");
        });
    }

    public function scopeWilayahKhusus($query) {
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
    public function scopeVersiTerbaru($query)
    {
        $versi_opensid = lastrelease('https://api.github.com/repos/OpenSID/opendk/releases/latest');

        if ($versi_opensid !== false) {
            $version = $versi_opensid->tag_name;
            $version = preg_replace('/[^0-9]/', '', $version);
            $version = substr($version, 0, 2).'.'.substr($version, 2, 2);
        }

        return $query->where('versi', 'like', "%{$version}%");
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

    public function scopeKabupatenKosong($query)
    {
        return $query
        ->selectRaw('kode_wilayah.nama_prov,kode_wilayah.nama_kab, kode_wilayah.kode_kab, count(kode_wilayah.nama_kec) as jumlah' )
        ->rightJoin('kode_wilayah', 'kode_wilayah.kode_kec', '=', 'opendk.kode_kecamatan')
        ->whereNull('opendk.nama_kecamatan')
        ->groupBy('kode_wilayah.kode_kec');
        // return DB::select("select a.region_code, a.region_name as nama_kabupaten, c.region_name as nama_provinsi, b.jml_desa from (select region_code, region_name from `tbl_regions` t left join desa d on t.region_name = d.nama_kabupaten where length(region_code) = 5 and region_name not like 'kota %' and d.id is null ) a left join (select left(region_code, 5) as kabupaten_code, left(region_code, 2) as provinsi_code, count(*) as jml_desa from tbl_regions where char_length(region_code) = 13 group by kabupaten_code, provinsi_code ) b on a.region_code = b.kabupaten_code left join tbl_regions c on c.region_code = b.provinsi_code order by a.region_code");
    }

}
