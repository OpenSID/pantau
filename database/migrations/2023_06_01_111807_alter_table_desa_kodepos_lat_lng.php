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
        Schema::table('desa', function (Blueprint $table) {
            $table->string('kode_pos', 6)->nullable()->change();
            $table->string('lat', 20)->nullable()->change();
            $table->string('lng', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('desa', function (Blueprint $table) {
            $table->string('kode_pos', 6)->change();
            $table->string('lat', 20)->change();
            $table->string('lng', 20)->change();
        });
    }
};
