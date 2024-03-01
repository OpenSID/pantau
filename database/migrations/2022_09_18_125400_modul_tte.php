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
            $table->tinyInteger('modul_tte')->after('telepon'); // modul tte
            $table->float('jml_surat_tte', 8, 0)->after('telepon'); // jumlah surat tte
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
            $table->dropColumn('modul_tte');
            $table->dropColumn('jml_surat_tte');
        });
    }
};
