<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(GrupSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SanctumAksesTokenSeeder::class);
        $this->call(RegionSeeder::class);
        $this->call(DesaSeeder::class);
        $this->call(AksesSeeder::class);
        $this->call(NotifikasiSeeder::class);
        $this->call(NotifikasiDesaSeeder::class);
    }
}
