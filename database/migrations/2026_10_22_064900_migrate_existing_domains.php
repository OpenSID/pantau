<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (! Schema::hasTable('domain_histories')) {
            return;
        }

        DB::statement('
            INSERT INTO domain_histories
                (desa_id, kode_desa, domain, domain_normalized, is_active, source, obtained_at, note, created_by, created_at, updated_at)
            SELECT
                d.id,
                d.kode_desa,
                d.url_hosting,
                LOWER(TRIM(d.url_hosting)),
                1,
                "import",
                COALESCE(d.domain_updated_at, d.updated_at, NOW()),
                "Migrasi dari data lama",
                NULL,
                NOW(),
                NOW()
            FROM desa d
            WHERE d.url_hosting IS NOT NULL
              AND d.url_hosting <> ""
              AND d.kode_desa IS NOT NULL
              AND NOT EXISTS (
                  SELECT 1 FROM domain_histories h
                  WHERE h.desa_id = d.id AND h.is_active = 1
              )
        ');

        // Sinkronkan desa.domain dengan domain_histories yang aktif.
        DB::statement('
            UPDATE desa d
            INNER JOIN domain_histories h ON h.desa_id = d.id AND h.is_active = 1
            SET d.domain = h.domain,
                d.domain_updated_at = COALESCE(d.domain_updated_at, h.obtained_at, NOW())
        ');
    }

    public function down()
    {
        // Riwayat domain tidak dihapus saat rollback.
    }
};
