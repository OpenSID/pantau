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
            if(!Schema::hasColumn('desa', 'sebutan_desa')){
                $table->string('sebutan_desa', 80)->default('Desa');
            }
            $table->string('layanan')->default('umum')->nullable()->after('sebutan_desa');
            // Tambahkan index untuk performa filter
            $table->index('layanan', 'layanan');
            $table->index('sebutan_desa', 'sebutan_desa');
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
            $table->dropIndex('layanan');
            $table->dropIndex('sebutan_desa');
            $table->dropColumn(['layanan']);
        });
    }
};
