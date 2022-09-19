<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBpsKemendagri extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'tbl_bps_kemendagri';

    /** {@inheritdoc} */
    protected $guarded = [];

    /** {@inheritdoc} */
    protected $fillable = [
        'kode_provinsi_kemendagri',
        'nama_provinsi_kemendagri',
        'kode_provinsi_bps',
        'nama_provinsi_bps',
        'kode_kabupaten_kemendagri',
        'nama_kabupaten_kemendagri',
        'kode_kabupaten_bps',
        'nama_kabupaten_bps',
        'kode_kecamatan_kemendagri',
        'nama_kecamatan_kemendagri',
        'kode_kecamatan_bps',
        'nama_kecamatan_bps',
        'kode_desa_kemendagri',
        'nama_desa_kemendagri',
        'kode_desa_bps',
        'nama_desa_bps',
    ];
}
