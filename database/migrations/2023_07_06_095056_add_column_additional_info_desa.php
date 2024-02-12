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
            $table->integer('jml_penduduk', false, true)->nullable()->default(0)->after('modul_tte');
            $table->integer('jml_artikel', false, true)->nullable()->default(0)->after('modul_tte');
            $table->integer('jml_surat_keluar', false, true)->nullable()->default(0)->after('modul_tte');
            $table->integer('jml_bantuan', false, true)->nullable()->default(0)->after('modul_tte');
            $table->integer('jml_mandiri', false, true)->nullable()->default(0)->after('modul_tte');
            $table->integer('jml_pengguna', false, true)->nullable()->default(0)->after('modul_tte');
            $table->integer('jml_unsur_peta', false, true)->nullable()->default(0)->after('modul_tte');
            $table->integer('jml_persil', false, true)->nullable()->default(0)->after('modul_tte');
            $table->integer('jml_dokumen', false, true)->nullable()->default(0)->after('modul_tte');
            $table->integer('jml_keluarga', false, true)->nullable()->default(0)->after('modul_tte');
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
            $table->dropColumn('jml_penduduk');
            $table->dropColumn('jml_artikel');
            $table->dropColumn('jml_surat_keluar');
            $table->dropColumn('jml_bantuan');
            $table->dropColumn('jml_mandiri');
            $table->dropColumn('jml_pengguna');
            $table->dropColumn('jml_unsur_peta');
            $table->dropColumn('jml_persil');
            $table->dropColumn('jml_dokumen');
            $table->dropColumn('jml_keluarga');
        });
    }
};
