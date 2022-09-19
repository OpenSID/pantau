<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModulTte extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('desa', function (Blueprint $table) {
            $table->tinyInteger('tte')->after('telepon'); // modul tte
            $table->float('surat_tte', 8, 0)->after('telepon'); // jumlah surat tte
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
