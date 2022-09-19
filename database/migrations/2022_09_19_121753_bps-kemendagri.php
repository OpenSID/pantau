<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class bpsKemendagri extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_bps_kemendagri', function (Blueprint $table) {
            $table->string('kode_provinsi_kemendagri', 100);
            $table->string('nama_provinsi_kemendagri', 100);
            $table->string('kode_provinsi_bps', 100);
            $table->string('nama_provinsi_bps', 100);
            $table->string('kode_kabupaten_kemendagri', 100);
            $table->string('nama_kabupaten_kemendagri', 100);
            $table->string('kode_kabupaten_bps', 100);
            $table->string('nama_kabupaten_bps', 100);
            $table->string('kode_kecamatan_kemendagri', 100);
            $table->string('nama_kecamatan_kemendagri', 100);
            $table->string('kode_kecamatan_bps', 100);
            $table->string('nama_kecamatan_bps', 100);
            $table->string('kode_desa_kemendagri', 100);
            $table->string('nama_desa_kemendagri', 100);
            $table->string('kode_desa_bps', 100);
            $table->string('nama_desa_bps', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_bps_kemendagri');
    }
}
