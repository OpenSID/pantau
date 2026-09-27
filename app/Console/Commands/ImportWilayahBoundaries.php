<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportWilayahBoundaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wilayah:import-boundaries 
                            {--level= : Import specific level only (prov, kab, kec, kel)}
                            {--fresh : Truncate table before import}
                            {--chunk=1000 : Batch size for inserts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import wilayah boundaries data from wilayah_boundaries-main repository';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║     Wilayah Boundaries Importer                          ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Check if wilayah_boundaries table exists
        if (! DB::getSchemaBuilder()->hasTable('wilayah_boundaries')) {
            $this->error('❌ Table "wilayah_boundaries" does not exist.');
            $this->info('💡 Run migration first: php artisan migrate');

            return Command::FAILURE;
        }

        // Fresh option - truncate table
        if ($this->option('fresh')) {
            if ($this->confirm('⚠️  This will truncate the wilayah_boundaries table. Continue?')) {
                DB::table('wilayah_boundaries')->truncate();
                $this->info('✅ Table truncated.');
                $this->newLine();
            } else {
                $this->info('⏹️  Import cancelled.');
                return Command::SUCCESS;
            }
        }

        // Call seeder
        try {
            $this->call('db:seed', [
                '--class' => 'WilayahBoundarySeeder',
                '--force' => true,
            ]);

            $this->newLine();
            $this->info('✅ Import completed successfully!');

            // Show statistics
            $this->showStatistics();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Import failed: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Show import statistics.
     *
     * @return void
     */
    private function showStatistics(): void
    {
        $this->info('📊 Import Statistics:');
        $this->table(
            ['Level', 'Count'],
            [
                ['Provinsi', DB::table('wilayah_boundaries')->where('level', 'prov')->count()],
                ['Kabupaten', DB::table('wilayah_boundaries')->where('level', 'kab')->count()],
                ['Kecamatan', DB::table('wilayah_boundaries')->where('level', 'kec')->count()],
                ['Kelurahan/Desa', DB::table('wilayah_boundaries')->where('level', 'kel')->count()],
                ['Total', DB::table('wilayah_boundaries')->count()],
            ]
        );
    }
}
