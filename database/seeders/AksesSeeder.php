<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AksesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = file_get_contents(base_path('database/factories/akses.sql'));
        $queries = explode(';', $sql);

        foreach ($queries as $query) {
            if (!empty(trim($query))) {
                try {
                    DB::unprepared($query . ';');
                } catch (\Illuminate\Database\QueryException $e) {
                    $errorCode = $e->errorInfo[1];

                    // Check if the error code indicates a duplicate entry
                    if ($errorCode === 1062) {
                        // Handle duplicate entry error
                        // For example, log the error, skip the query, or perform custom actions
                        // Log::error('Duplicate entry error: ' . $e->getMessage());
                    } else {
                        // Re-throw the exception for other types of errors
                        throw $e;
                    }
                }
            }
        }

        DB::table('akses')->update(['created_at' => now()]);
    }
}
