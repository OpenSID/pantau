<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = file_get_contents(base_path('database/factories/region.sql'));
        $queries = explode(';', $sql);

        foreach ($queries as $query) {
            if (!empty(trim($query))) {
                DB::unprepared($query);
            }
        }
    }
}
