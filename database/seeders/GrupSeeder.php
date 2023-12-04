<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserGrup;

class GrupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userGrups = [
            ['id' => 1, 'nama' => 'Administrator'],
            ['id' => 2, 'nama' => 'Operator'],
            ['id' => 3, 'nama' => 'Redaksi'],
            ['id' => 4, 'nama' => 'Kontributor'],
            ['id' => 5, 'nama' => 'Satgas Covid-19'],
        ];

        foreach ($userGrups as $userGrup) {
            // Check if a record with the same ID exists
            $existingRecord = UserGrup::find($userGrup['id']);

            // If the record doesn't exist, insert it
            if (!$existingRecord) {
                UserGrup::create([
                    'id' => $userGrup['id'],
                    'nama' => $userGrup['nama'],
                    'created_at' => now(),
                ]);
            }
        }
    }
}
