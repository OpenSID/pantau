<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Seed wilayah boundaries data after table is created
        try {
            $exitCode = Artisan::call('db:seed', [
                '--class' => 'WilayahBoundarySeeder',
                '--force' => true,
            ]);
            
            // Log the output for debugging
            $output = Artisan::output();
            if (!empty($output)) {
                // Write to log file for debugging
                file_put_contents(storage_path('logs/wilayah_boundary_seeder.log'), $output);
            }
            
            if ($exitCode !== 0) {
                // Log error if seeder failed
                file_put_contents(storage_path('logs/wilayah_boundary_seeder.log'), "Seeder failed with exit code: {$exitCode}\nOutput: {$output}", FILE_APPEND);
            }
        } catch (\Exception $e) {
            // Log the exception for debugging
            file_put_contents(storage_path('logs/wilayah_boundary_seeder.log'), "Exception: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString(), FILE_APPEND);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear boundaries data when rolling back
        DB::table('wilayah_boundaries')->truncate();
    }
};
