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

        $files = File::files($directory);
        $totalFiles = count($files);
        
        $this->command->info("📁 Importing {$level} boundaries ({$totalFiles} files)...");

        $fileCount = 0;
        $recordCount = 0;

        foreach ($files as $index => $file) {
            $count = $this->importFile($file->getPathname(), $level);
            $recordCount += $count;
            $fileCount++;

            // Progress indicator every 5 files
            if ($fileCount % 5 === 0 || $fileCount === $totalFiles) {
                $this->command->info("   Progress: {$fileCount}/{$totalFiles} files ({$recordCount} records)");
            }
        }

        $this->command->info("   ✅ Imported {$fileCount} files ({$recordCount} records)");
        
        return $recordCount;
    }

    /**
     * Import a single SQL file.
     *
     * @param  string  $filePath
     * @param  string  $level
     * @return int
     */
    private function importFile(string $filePath, string $level): int
    {
        $sqlContent = File::get($filePath);

        // Remove comments
        $sqlContent = $this->cleanSql($sqlContent);

        // Modify INSERT statements to remove 'nama' column and add 'level'
        $sqlContent = $this->modifyInsertStatements($sqlContent, $level);

        // Split into individual statements
        $statements = $this->parseSqlStatements($sqlContent);

        $recordCount = 0;

        DB::transaction(function () use ($statements, &$recordCount, $filePath) {
            foreach ($statements as $statement) {
                if (empty(trim($statement))) {
                    continue;
                }

                try {
                    // Execute DELETE or INSERT statements
                    DB::unprepared($statement);

                    // Count INSERT statements
                    if (stripos($statement, 'INSERT') === 0) {
                        // Count VALUES
                        preg_match_all('/VALUES\s*\((.+?)\)(?:,|\s*$)/s', $statement, $matches);
                        if (!empty($matches[1])) {
                            $recordCount += count($matches[1]);
                        }
                    }
                } catch (\Exception $e) {
                    $this->command->error("   ❌ SQL Error in {$filePath}: " . $e->getMessage());
                    throw $e;
                }
            }
        });

        return $recordCount;
    }

    /**
     * Modify INSERT statements to remove 'nama' column and add 'level'.
     *
     * @param  string  $sqlContent
     * @param  string  $level
     * @return string
     */
    private function modifyInsertStatements(string $sqlContent, string $level): string
    {
        // Replace INSERT INTO wilayah_boundaries(kode,nama,lat,lng,path)
        // with INSERT INTO wilayah_boundaries(kode,level,lat,lng,path)
        $sqlContent = preg_replace(
            '/INSERT INTO wilayah_boundaries\s*\(\s*kode\s*,\s*nama\s*,\s*lat\s*,\s*lng\s*,\s*path\s*\)/i',
            'INSERT INTO wilayah_boundaries(kode,level,lat,lng,path)',
            $sqlContent
        );

        // Replace VALUES ('XX','Name',lat,lng,'path')
        // with VALUES ('XX','level',lat,lng,'path')
        // This regex matches VALUES clauses and replaces the second value (nama) with the level
        $sqlContent = preg_replace_callback(
            '/VALUES\s*\(\s*\'([^\']+)\'\s*,\s*\'[^\']+\'\s*,\s*([0-9.\-]+)\s*,\s*([0-9.\-]+)\s*,\s*\'(.+)\'\s*\)/sU',
            function ($matches) use ($level) {
                $kode = $matches[1];
                $lat = $matches[2];
                $lng = $matches[3];
                $path = $matches[4];
                return "VALUES ('{$kode}','{$level}',{$lat},{$lng},'{$path}')";
            },
            $sqlContent
        );

        return $sqlContent;
    }

    /**
     * Clean SQL content by removing comments.
     *
     * @param  string  $sql
     * @return string
     */
    private function cleanSql(string $sql): string
    {
        // Remove multi-line comments
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Remove single-line comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        
        return trim($sql);
    }

    /**
     * Parse SQL content into individual statements.
     *
     * @param  string  $sql
     * @return array
     */
    private function parseSqlStatements(string $sql): array
    {
        $statements = [];
        $currentStatement = '';
        $inString = false;
        $stringChar = null;
        
        $length = strlen($sql);
        
        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $prevChar = $i > 0 ? $sql[$i - 1] : null;
            
            // Handle string literals
            if (($char === "'" || $char === '"') && $prevChar !== '\\') {
                if (!$inString) {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char === $stringChar) {
                    $inString = false;
                    $stringChar = null;
                }
            }
            
            // Split on semicolon only outside strings
            if ($char === ';' && !$inString) {
                if (!empty(trim($currentStatement))) {
                    $statements[] = trim($currentStatement);
                }
                $currentStatement = '';
            } else {
                $currentStatement .= $char;
            }
        }
        
        // Add last statement if exists
        if (!empty(trim($currentStatement))) {
            $statements[] = trim($currentStatement);
        }
        
        return $statements;
    }
}
