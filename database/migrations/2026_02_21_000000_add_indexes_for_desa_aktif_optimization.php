<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('akses', function (Blueprint $table) {
            $table->index(['desa_id', 'created_at'], 'idx_akses_desa_created');
        });

        Schema::table('desa', function (Blueprint $table) {
            $table->index('updated_at', 'idx_desa_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('akses', function (Blueprint $table) {
            $table->dropIndex('idx_akses_desa_created');
        });

        Schema::table('desa', function (Blueprint $table) {
            $table->dropIndex('idx_desa_updated_at');
        });
    }
};
