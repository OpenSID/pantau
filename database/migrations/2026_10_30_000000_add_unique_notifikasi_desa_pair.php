<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Hapus duplikat (id_notifikasi, id_desa) yang sudah ada, lalu pasang unique index
     * agar upsert pada NotifikasiDesa aman dari race condition.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            DELETE FROM notifikasi_desa
            WHERE id NOT IN (
                SELECT id FROM (
                    SELECT MIN(id) AS id FROM notifikasi_desa GROUP BY id_notifikasi, id_desa
                ) AS keep_rows
            )
        ');

        Schema::table('notifikasi_desa', function (Blueprint $table) {
            $table->unique(['id_notifikasi', 'id_desa'], 'notifikasi_desa_notifikasi_desa_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifikasi_desa', function (Blueprint $table) {
            $table->dropUnique('notifikasi_desa_notifikasi_desa_unique');
        });
    }
};
