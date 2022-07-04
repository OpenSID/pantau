<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_grup = array(
            array(
                "id" => 1,
                "nama" => "Administrator",
                "created_at" => now(),
            ),
            array(
                "id" => 2,
                "nama" => "Operator",
                "created_at" => now(),
            ),
            array(
                "id" => 3,
                "nama" => "Redaksi",
                "created_at" => now(),
            ),
            array(
                "id" => 4,
                "nama" => "Kontributor",
                "created_at" => now(),
            ),
            array(
                "id" => 5,
                "nama" => "Satgas Covid-19",
                "created_at" => now(),
            ),
        );

        DB::table('user_grup')->insert($user_grup);
    }
}
