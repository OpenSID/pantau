<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SanctumAksesTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $personal_access_tokens = [
            [
                "id" => 1,
                "tokenable_type" => \App\Models\User::class,
                "tokenable_id" => 6,
                "name" => "pantau-sid",
                "token" => "009652573bc23cec5e761bce432722c472936376432065703d1fa5f2aea26201",
                "abilities" => '["pantau-wilayah","pantau-track"]',
                "last_used_at" => "2021-12-24 03:20:06",
                "created_at" => "2021-12-24 03:13:52",
                "updated_at" => "2021-12-24 03:20:06",
            ],
        ];

        foreach ($personal_access_tokens as $token) {
            // Use updateOrInsert to insert or update the token
            DB::table('personal_access_tokens')->updateOrInsert(
                ['id' => $token['id']], // Search condition
                $token
            );
        }
    }
}
