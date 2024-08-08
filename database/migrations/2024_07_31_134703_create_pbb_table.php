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
        Schema::create('pbb', function (Blueprint $table) {
            $table->id();
            $table->string('kode_desa', 25)->unique();
            $table->string('kode_kecamatan', 25);
            $table->string('kode_kabupaten', 25);
            $table->string('kode_provinsi', 25);
            $table->string('nama_desa', 100);
            $table->string('nama_kecamatan', 100);
            $table->string('nama_kabupaten', 100);
            $table->string('nama_provinsi', 100);   
            $table->string('url', 100)->nullable();
            $table->string('versi', 25);
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
        Schema::dropIfExists('pbb');
    }
};
