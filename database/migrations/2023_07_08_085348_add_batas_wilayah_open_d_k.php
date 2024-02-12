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
            $table->json('batas_wilayah')->nullable()->after('sebutan_wilayah');
            $table->json('desa')->nullable()->after('sebutan_wilayah');
            $table->string('lat', 20)->nullable()->after('sebutan_wilayah');
            $table->string('lng', 20)->nullable()->after('sebutan_wilayah');
            $table->smallInteger('jumlah_bantuan',false, true)->nullable()->default(0)->after('sebutan_wilayah');
            $table->smallInteger('jumlahdesa_sinkronisasi',false, true)->nullable()->default(0)->after('sebutan_wilayah');
            $table->string('alamat', 255)->nullable()->after('sebutan_wilayah');
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
            $table->dropColumn('batas_wilayah');
            $table->dropColumn('desa');
            $table->dropColumn('lat');
            $table->dropColumn('lng');
            $table->dropColumn('jumlah_bantuan');
            $table->dropColumn('jumlahdesa_sinkronisasi');
            $table->dropColumn('alamat');
        });
    }
};
