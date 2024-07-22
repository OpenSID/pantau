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
        Schema::table('openkab', function (Blueprint $table) {
            $table->string('kode_prov', 25)->index();
            $table->string('nama_prov', 100);
            $table->string('nama_aplikasi', 255);
            $table->string('sebutan_kab', 100);
            $table->string('url', 255);
            $table->string('versi', 25);
            $table->integer('jumlah_desa')->default(0);
            $table->integer('jumlah_penduduk')->default(0);
            $table->integer('jumlah_keluarga')->default(0);
            $table->integer('jumlah_rtm')->default(0);
            $table->integer('jumlah_bantuan')->default(0);
            $table->dateTime('tgl_rekam')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('openkab', function (Blueprint $table) {
            $table->dropColumn('kode_prov');
            $table->dropColumn('nama_prov');
            $table->dropColumn('nama_aplikasi');
            $table->dropColumn('sebutan_kab');
            $table->dropColumn('url');
            $table->dropColumn('versi');
            $table->dropColumn('jumlah_desa');
            $table->dropColumn('jumlah_penduduk');
            $table->dropColumn('jumlah_keluarga');
            $table->dropColumn('jumlah_rtm');
            $table->dropColumn('jumlah_bantuan');
            $table->dropColumn('tgl_rekam');
        });
    }
};
