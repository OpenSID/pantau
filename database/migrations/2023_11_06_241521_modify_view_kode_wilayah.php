<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
    CREATE OR REPLACE VIEW kode_wilayah AS
    select
        d.id AS id,
        p.region_code AS kode_prov,
        if(p.new_region_name is null, p.region_name, p.new_region_name) AS nama_prov,
        kab.region_code AS kode_kab,
        if(kab.new_region_name is null,kab.region_name,kab.new_region_name) AS nama_kab,
        kec.region_code AS kode_kec,
        if(kec.new_region_name is null,kec.region_name,kec.new_region_name) AS nama_kec,
        d.region_code AS kode_desa,
        if(d.new_region_name is null,d.region_name,d.new_region_name) AS nama_desa,
        d.desa_id AS desa_id
    from
        tbl_regions d
    left join tbl_regions kec on d.parent_code = kec.region_code
    left join tbl_regions kab on kec.parent_code = kab.region_code
    left join tbl_regions p on kab.parent_code = p.region_code
    where
        char_length(d.region_code) = 13
SQL;
DB::statement($sql);
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
};
