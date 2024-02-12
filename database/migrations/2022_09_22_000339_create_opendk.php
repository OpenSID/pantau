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
        Schema::create('opendk', function (Blueprint $table) {
            $table->string('kode_kecamatan', 25)->unique();
            $table->string('kode_kabupaten', 25)->index();
            $table->string('kode_provinsi', 25)->index();
            $table->string('nama_kecamatan', 100);
            $table->string('nama_kabupaten', 100);
            $table->string('nama_provinsi', 100);
            $table->string('url', 255);
            $table->string('versi', 25)->nullable();
            $table->float('jml_desa', 8,0)->nullable();
            $table->float('jumlah_penduduk', 65,  0)->nullable();
            $table->float('jumlah_keluarga', 65,  0)->nullable();
            $table->longText('peta_wilayah')->nullable();
            $table->string('sebutan_wilayah')->nullable();
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
        Schema::dropIfExists('opendk');
    }
};
