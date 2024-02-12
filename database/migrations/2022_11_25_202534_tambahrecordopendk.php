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
        Schema::table('opendk', function (Blueprint $table) {
            $table->dateTime('tgl_rekam')->after('sebutan_wilayah')->nullable(true);
            $table->string('nama_camat', 100)->after('sebutan_wilayah')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opendk', function (Blueprint $table) {
            $table->dropColumn('tgl_rekam');
            $table->dropColumn('nama_camat');
        });
    }
};
