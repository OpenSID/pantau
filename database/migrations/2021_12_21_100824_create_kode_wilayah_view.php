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
    public function up()
    {
        DB::statement("CREATE OR REPLACE VIEW `kode_wilayah` AS select `d`.`id` AS `id`,`p`.`region_code` AS `kode_prov`,`p`.`region_name` AS `nama_prov`,`kab`.`region_code` AS `kode_kab`,`kab`.`region_name` AS `nama_kab`,`kec`.`region_code` AS `kode_kec`,`kec`.`region_name` AS `nama_kec`,`d`.`region_code` AS `kode_desa`,`d`.`region_name` AS `nama_desa`,`d`.`desa_id` AS `desa_id` from (((`tbl_regions` `d` left join `tbl_regions` `kec` on((`d`.`parent_code` = `kec`.`region_code`))) left join `tbl_regions` `kab` on((`kec`.`parent_code` = `kab`.`region_code`))) left join `tbl_regions` `p` on((`kab`.`parent_code` = `p`.`region_code`))) where (char_length(`d`.`region_code`) = 13)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS kode_wilayah");
    }
};
