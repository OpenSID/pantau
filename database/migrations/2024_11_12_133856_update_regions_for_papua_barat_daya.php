<?php

use Illuminate\Support\Facades\DB;
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
        DB::table('tbl_regions')->insertOrIgnore([
            'region_code' => '96',
            'region_name' => 'PAPUA BARAT DAYA',
            'new_region_name' => null,
            'keterangan' => 'Provinsi baru',
            'parent_code' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $provinsi = [
            '92.01', // Kabupaten Sorong
            '92.04', // Kabupaten Sorong Selatan
            '92.05', // Kabupaten Raja Ampat
            '92.09', // Kabupaten Tambrauw
            '92.10', // Kabupaten Maybrat
            '92.71'  // Kota Sorong
        ];

        foreach ($provinsi as $prov) {
            DB::table('tbl_regions')
                ->where('region_code', $prov)
                ->update([
                    'parent_code' => '96',
                    'updated_at'  => now()
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('tbl_regions')
            ->where('region_code', '96')
            ->delete();

        $provinsi = [
            '92.01', // Kabupaten Sorong
            '92.04', // Kabupaten Sorong Selatan
            '92.05', // Kabupaten Raja Ampat
            '92.09', // Kabupaten Tambrauw
            '92.10', // Kabupaten Maybrat
            '92.71'  // Kota Sorong
        ];

        foreach ($provinsi as $prov) {
            DB::table('tbl_regions')
                ->where('region_code', $prov)
                ->update([
                    'parent_code' => '92'
                ]);
        }
    }
};
