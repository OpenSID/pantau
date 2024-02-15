<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePantauKeloladesa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('track_keloladesa', function (Blueprint $table) {
            $table->string('id_device', 25)->unique();
            $table->string('kode_desa', 25)->index();
            $table->date('tgl_akses');
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
        Schema::dropIfExists('pantau_keloladesa');
    }
}
