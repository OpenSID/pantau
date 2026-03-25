<?php

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
        Schema::create('wilayah_boundaries', function (Blueprint $table) {
            // Primary key: kode (natural key, also serves as unique index)
            $table->string('kode', 13)->primary();
            
            // Name of the wilayah
            $table->string('nama', 100)->nullable();
            
            // Level hierarchy: prov, kab, kec, kel
            $table->string('level', 4)->nullable()->index();
            
            // Centroid coordinates for quick reference
            $table->double('lat', 10, 8)->nullable();
            $table->double('lng', 11, 8)->nullable();
            
            // Boundary path (GeoJSON coordinates or encoded polygon)
            $table->longText('path')->nullable();
            
            // Status flag (1=active, 0=inactive, or custom status)
            $table->tinyInteger('status')->default(1);
            
            // Timestamps
            $table->timestamp('updated_at')->nullable();                        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wilayah_boundaries');
    }
};
