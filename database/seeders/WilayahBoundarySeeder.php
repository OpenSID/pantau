<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WilayahBoundarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $basePath = base_path('database/wilayah_boundaries-main/db');

        $this->command->info('🚀 Starting Wilayah Boundary Import...');
        $this->command->info('─────────────────────────────────────────');

        // Import in order: prov -> kab -> kec -> kel
        $levels = [
            'prov' => 'prov',
            'kab'  => 'kab',
            'kec'  => 'kec',
            'kel'  => 'kel',
        ];

        $totalRecords = 0;
        $startTime = microtime(true);

        foreach ($levels as $dir => $level) {
            $count = $this->importLevel($basePath . '/' . $dir, $level);
            $totalRecords += $count;
        }

        // Update level column based on kode patterns
        $this->updateLevels();

        $duration = round(microtime(true) - $startTime, 2);

        $this->command->info('─────────────────────────────────────────');
        $this->command->info("✅ Imported {$totalRecords} records in {$duration}s");
    }

    /**
     * Import boundaries for a specific level.
     *
     * @param  string  $directory
     * @param  string  $level
     * @return int
     */
    private function importLevel(string $directory, string $level): int
    {
        if (!File::exists($directory)) {
            $this->command->warn("⚠️  Directory not found: {$directory}");
            return 0;
        }

        $recordCount = 0;

        // For 'kel' level, scan subdirectories (kel/11/*.sql, kel/12/*.sql, etc.)
        if ($level === 'kel') {
            $recordCount = $this->importKelurahanRecursively($directory);
        } else {
            // For prov, kab, kec - process files directly in directory
            $files = File::files($directory);
            $totalFiles = count($files);

            $this->command->info("📁 Importing {$level} boundaries ({$totalFiles} files)...");

            $fileCount = 0;

            foreach ($files as $index => $file) {
                $count = $this->importSqlFile($file->getPathname());
                $recordCount += $count;
                $fileCount++;

                // Progress indicator every 5 files
                if ($fileCount % 5 === 0 || $fileCount === $totalFiles) {
                    $this->command->info("   Progress: {$fileCount}/{$totalFiles} files ({$recordCount} records)");
                }
            }

            $this->command->info("   ✅ Imported {$fileCount} files ({$recordCount} records)");
        }

        return $recordCount;
    }

    /**
     * Import kelurahan boundaries recursively from subdirectories.
     *
     * @param  string  $baseDirectory
     * @return int
     */
    private function importKelurahanRecursively(string $baseDirectory): int
    {
        $this->command->info("📁 Importing kel boundaries (scanning subdirectories)...");

        $recordCount = 0;
        $fileCount = 0;
        
        // Get all subdirectories (11, 12, 13, etc.)
        $subdirs = array_filter(File::directories($baseDirectory), function ($dir) {
            return is_numeric(basename($dir));
        });

        $totalSubdirs = count($subdirs);
        $this->command->info("   Found {$totalSubdirs} province subdirectories");

        foreach ($subdirs as $index => $subdir) {
            $provCode = basename($subdir);
            $files = File::files($subdir);
            $subdirFileCount = count($files);

            foreach ($files as $file) {
                $count = $this->importSqlFile($file->getPathname());
                $recordCount += $count;
                $fileCount++;
            }

            // Progress indicator every 5 subdirectories
            if (($index + 1) % 5 === 0 || ($index + 1) === $totalSubdirs) {
                $this->command->info("   Progress: " . ($index + 1) . "/{$totalSubdirs} provinces ({$fileCount} files, {$recordCount} records)");
            }
        }

        $this->command->info("   ✅ Imported {$fileCount} files ({$recordCount} records)");

        return $recordCount;
    }

    /**
     * Import a single SQL file directly.
     *
     * @param  string  $filePath
     * @return int
     */
    private function importSqlFile(string $filePath): int
    {
        try {
            // Read and execute the SQL file directly
            $sql = File::get($filePath);
            
            // Menggunakan DB::unprepared() karena:
            // 1. File SQL mengandung multiple statements (DELETE + INSERT)
            // 2. Data polygon sangat besar (longText), tidak cocok untuk prepared statement
            // 3. Tidak perlu parameter binding karena data sudah hard-coded
            // 4. Lebih cepat untuk bulk operations
            DB::unprepared($sql);
            
            // Count INSERT statements to estimate records imported
            $recordCount = substr_count($sql, 'INSERT INTO');
            
            return $recordCount;
        } catch (\Exception $e) {
            $this->command->error("   ❌ Error importing {$filePath}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Update the level column based on kode patterns.
     *
     * @return void
     */
    private function updateLevels(): void
    {
        $this->command->info('🔄 Updating level column based on kode patterns...');
        
        try {
            // Update provinces (kode pattern: XX)
            $provCount = DB::table('wilayah_boundaries')
                ->whereRaw('LENGTH(kode) = 2')
                ->whereNull('level')
                ->update(['level' => 'prov']);
                
            // Update regencies (kode pattern: XX.XX)
            $kabCount = DB::table('wilayah_boundaries')
                ->whereRaw('LENGTH(kode) = 5 AND kode LIKE "%.%"')
                ->whereNull('level')
                ->update(['level' => 'kab']);
                
            // Update districts (kode pattern: XX.XX.XX)
            $kecCount = DB::table('wilayah_boundaries')
                ->whereRaw('LENGTH(kode) = 8 AND kode LIKE "%.%.%"')
                ->whereNull('level')
                ->update(['level' => 'kec']);
                
            // Update villages (kode pattern: XX.XX.XX.XXXX)
            $kelCount = DB::table('wilayah_boundaries')
                ->whereRaw('LENGTH(kode) = 13 AND kode LIKE "%.%.%.%"')
                ->whereNull('level')
                ->update(['level' => 'kel']);
                
            $this->command->info("   ✅ Updated: {$provCount} provinces, {$kabCount} regencies, {$kecCount} districts, {$kelCount} villages");
        } catch (\Exception $e) {
            $this->command->error("   ❌ Error updating levels: " . $e->getMessage());
        }
    }
}
