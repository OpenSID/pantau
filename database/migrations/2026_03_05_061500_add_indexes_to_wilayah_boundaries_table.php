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
        Schema::table('wilayah_boundaries', function (Blueprint $table) {
            // Add composite index for level and kode to improve query performance
            $table->index(['level', 'kode'], 'wilayah_boundaries_level_kode_index');
            
            // Add index for status to filter active boundaries
            $table->index('status', 'wilayah_boundaries_status_index');
            
            // Note: We cannot add index on path column directly because it's a LONGTEXT
            // MySQL requires a prefix length for TEXT/BLOB columns in indexes
            // For performance, we'll rely on the composite index and proper query optimization
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wilayah_boundaries', function (Blueprint $table) {
            $table->dropIndex('wilayah_boundaries_level_kode_index');
            $table->dropIndex('wilayah_boundaries_status_index');
        });
    }
};