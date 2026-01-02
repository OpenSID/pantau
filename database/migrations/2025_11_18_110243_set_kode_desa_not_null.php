<?php

use App\Models\Desa;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Desa::whereNull('kode_desa')->delete();
        Schema::table('desa', function(Blueprint $table){
            $table->string('kode_desa')->nullable(false)->change();            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desa', function(Blueprint $table){
            $table->string('kode_desa')->nullable(true)->change();            
        });
    }
};
