<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR REPLACE VIEW `kode_wilayah` AS SELECT `d`.`id` AS `id`, `p`.`region_code` AS `kode_prov`, IF (`p`.`new_region_name` IS NULL, `p`.`region_name`, `p`.`new_region_name`) AS `nama_prov`, `kab`.`region_code` AS `kode_kab`, IF (`kab`.`new_region_name` IS NULL, `kab`.`region_name`, `kab`.`new_region_name`) AS `nama_kab`, `kec`.`region_code` AS `kode_kec`, IF (`kec`.`new_region_name` IS NULL, `kec`.`region_name`, `kec`.`new_region_name`) AS `nama_kec`, `d`.`region_code` AS `kode_desa`, IF (`d`.`new_region_name` IS NULL, `d`.`region_name`, `d`.`new_region_name`) AS `nama_desa`, `d`.`desa_id` AS `desa_id` FROM (((`tbl_regions` `d` left join `tbl_regions` `kec` on(`d`.`parent_code` = `kec`.`region_code`)) left join `tbl_regions` `kab` on(`kec`.`parent_code` = `kab`.`region_code`)) left join `tbl_regions` `p` on(`kab`.`parent_code` = `p`.`region_code`)) WHERE char_length(`d`.`region_code`) = 13");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("CREATE OR REPLACE VIEW `kode_wilayah` AS select `d`.`id` AS `id`,`p`.`region_code` AS `kode_prov`,`p`.`region_name` AS `nama_prov`,`kab`.`region_code` AS `kode_kab`,`kab`.`region_name` AS `nama_kab`,`kec`.`region_code` AS `kode_kec`,`kec`.`region_name` AS `nama_kec`,`d`.`region_code` AS `kode_desa`,`d`.`region_name` AS `nama_desa`,`d`.`desa_id` AS `desa_id` from (((`tbl_regions` `d` left join `tbl_regions` `kec` on((`d`.`parent_code` = `kec`.`region_code`))) left join `tbl_regions` `kab` on((`kec`.`parent_code` = `kab`.`region_code`))) left join `tbl_regions` `p` on((`kab`.`parent_code` = `p`.`region_code`))) where (char_length(`d`.`region_code`) = 13)");
    }
};
