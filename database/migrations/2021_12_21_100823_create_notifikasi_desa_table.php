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
        Schema::create('notifikasi_desa', function (Blueprint $table) {
            $table->integer('id', true);
            $table->foreignId('id_desa')->references('id')->on('desa')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_notifikasi')->references('id')->on('notifikasi')->onUpdate('cascade')->onDelete('cascade');
            $table->tinyInteger('status')->default(0);
            $table->timestamp('tgl_kirim')->nullable();
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
        Schema::dropIfExists('notifikasi_desa');
    }
};
