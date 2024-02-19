<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bps_kemendagri_provinsi', function (Blueprint $table) {
            $table->string('kode_provinsi_kemendagri', 100)->unique();
            $table->string('nama_provinsi_kemendagri', 100);
            $table->string('kode_provinsi_bps', 100)->unique();
            $table->string('nama_provinsi_bps', 100);
            $table->timestamps();
        });

        Schema::create('bps_kemendagri_kabupaten', function (Blueprint $table) {
            $table->string('kode_provinsi_kemendagri', 100)->nullable()->index();
            $table->string('kode_kabupaten_kemendagri', 100)->unique();
            $table->string('nama_kabupaten_kemendagri', 100);
            $table->string('kode_kabupaten_bps', 100)->unique();
            $table->string('nama_kabupaten_bps', 100);
            $table->timestamps();
        });

        Schema::create('bps_kemendagri_kecamatan', function (Blueprint $table) {
            $table->string('kode_kabupaten_kemendagri', 100)->nullable()->index();
            $table->string('kode_kecamatan_kemendagri', 100)->unique();
            $table->string('nama_kecamatan_kemendagri', 100);
            $table->string('kode_kecamatan_bps', 100)->unique();
            $table->string('nama_kecamatan_bps', 100);
            $table->timestamps();
        });

        Schema::create('bps_kemendagri_desa', function (Blueprint $table) {
            $table->string('kode_kecamatan_kemendagri', 100)->nullable()->index();
            $table->string('kode_desa_kemendagri', 100)->unique();
            $table->string('nama_desa_kemendagri', 100);
            $table->string('kode_desa_bps', 100)->unique();
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
        Schema::dropIfExists('bps_kemendagri_provinsi');
        Schema::dropIfExists('bps_kemendagri_kabupaten');
        Schema::dropIfExists('bps_kemendagri_kecamatan');
        Schema::dropIfExists('bps_kemendagri_desa');
    }
};
