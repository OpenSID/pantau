<?php

use App\Models\UserGrup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        UserGrup::create([
            'nama' => 'Admin Wilayah'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('user_grup')->where('nama', 'Admin Wilayah')->delete();
    }
};
