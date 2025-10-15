<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create view untuk track_mobile join dengan tabel desa
        DB::statement("
            CREATE OR REPLACE VIEW track_mobile_view AS
            SELECT
                tm.id,
                tm.kode_desa,
                tm.tgl_akses,
                tm.created_at,
                tm.updated_at,
                d.nama_desa,
                d.kode_kecamatan,
                d.nama_kecamatan,
                d.kode_kabupaten,
                d.nama_kabupaten,
                d.kode_provinsi,
                d.nama_provinsi
            FROM track_mobile tm
            JOIN desa d ON tm.kode_desa = d.kode_desa
        ");

        // Create view untuk track_kelola_desa join dengan tabel desa
        DB::statement("
            CREATE OR REPLACE VIEW track_kelola_desa_view AS
            SELECT
                tkd.id_device as id,
                tkd.kode_desa,
                tkd.tgl_akses,
                tkd.created_at,
                tkd.updated_at,
                d.nama_desa,
                d.kode_kecamatan,
                d.nama_kecamatan,
                d.kode_kabupaten,
                d.nama_kabupaten,
                d.kode_provinsi,
                d.nama_provinsi
            FROM track_keloladesa tkd
            JOIN desa d ON tkd.kode_desa = d.kode_desa
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop views
        DB::statement('DROP VIEW IF EXISTS track_mobile_view');
        DB::statement('DROP VIEW IF EXISTS track_kelola_desa_view');
    }
};
