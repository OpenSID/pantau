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
        Schema::create('desa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_desa', 100);
            $table->string('kode_desa')->nullable()->index();
            $table->string('kode_pos', 6);
            $table->string('nama_kecamatan', 100)->index();
            $table->string('kode_kecamatan')->index();
            $table->string('nama_kabupaten', 100)->index();
            $table->string('kode_kabupaten')->index();
            $table->string('nama_provinsi', 100)->index();
            $table->string('kode_provinsi')->index();
            $table->string('lat', 20);
            $table->string('lng', 20);
            $table->string('alamat_kantor', 200);
            $table->tinyInteger('jenis')->nullable()->default(1);
            $table->string('ip_lokal', 20)->nullable();
            $table->string('ip_hosting', 20)->nullable();
            $table->string('versi_lokal', 20)->nullable();
            $table->string('versi_hosting', 20)->nullable();
            $table->timestamp('tgl_rekam_lokal')->nullable();
            $table->timestamp('tgl_rekam_hosting')->nullable();
            $table->timestamp('tgl_akses_lokal')->nullable();
            $table->timestamp('tgl_akses_hosting')->nullable();
            $table->string('url_lokal', 200)->nullable();
            $table->string('url_hosting', 200)->nullable();
            $table->boolean('opensid_valid')->unsigned()->nullable()->default(true);
            $table->string('email_desa', 50)->nullable();
            $table->string('telepon', 50)->nullable();
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
        Schema::dropIfExists('desa');
    }
};
