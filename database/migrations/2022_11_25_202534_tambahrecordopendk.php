<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Tambahrecordopendk extends Migration
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
            $table->json('desa')->after('sebutan_wilayah')->nullable(true);
            $table->json('batas_wilayah')->after('sebutan_wilayah')->nullable(true);
            $table->string('alamat', 100)->after('sebutan_wilayah')->nullable(true);
            $table->smallInteger('jumlahdesa_sinkronisasi')->after('sebutan_wilayah')->nullable(true);
            $table->integer('jumlah_bantuan')->after('sebutan_wilayah')->nullable(true);
            $table->string('nama_camat', 100)->after('sebutan_wilayah')->nullable(true);
            $table->renameColumn('jml_desa', 'jumlah_desa')->nullable(true);
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
            $table->dropColumn('desa');
            $table->dropColumn('batas_wilayah');
            $table->dropColumn('alamat');
            $table->dropColumn('jumlahdesa_sinkronisasi');
            $table->dropColumn('nama_camat');
            $table->renameColumn('jumlah_desa', 'jml_desa');
        });
    }
}
