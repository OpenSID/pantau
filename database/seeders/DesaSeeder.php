<?php

namespace Database\Seeders;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sql = file_get_contents(base_path('database/factories/desa.sql'));
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


        DB::table('desa as d')
            ->leftJoin('kode_wilayah as k', function($query) {
                $query
                    ->on(DB::raw("lower(d.nama_desa)"), '=', DB::raw("lower(k.nama_desa)"))
                    ->on(DB::raw("lower(d.nama_kecamatan)"), '=', DB::raw("lower(k.nama_kec)"))
                    ->on(function($query) {
                        $query
                            ->orOn(DB::raw("lower(d.nama_kabupaten)"), '=', DB::raw("lower(k.nama_kab)"))
                            ->orOn(DB::raw("lower(d.nama_kabupaten)"), '=', DB::raw("lower(replace(k.nama_kab, 'KAB ', ''))"))
                            ->orOn(DB::raw("lower(d.nama_kabupaten)"), '=', DB::raw("lower(replace(k.nama_kab, 'KOTA ', ''))"));
                    })
                    ->on(DB::raw("lower(d.nama_provinsi)"), '=', DB::raw("lower(k.nama_prov)"));
            })
            ->update([
                'd.kode_desa' => DB::raw('k.kode_desa'),
                'd.kode_kecamatan' => DB::raw('k.kode_kec'),
                'd.kode_kabupaten' => DB::raw('k.kode_kab'),
                'd.kode_provinsi' => DB::raw('k.kode_prov'),
            ]);

        DB::table('desa')->where('tgl_rekam_lokal', '<', '0000-01-01 00:00:00')->update(['tgl_rekam_lokal' => null]);
        DB::table('desa')->where('tgl_rekam_hosting', '<', '0000-01-01 00:00:00')->update(['tgl_rekam_hosting' => null]);
        DB::table('desa')->where('tgl_akses_lokal', '<', '0000-01-01 00:00:00')->update(['tgl_akses_lokal' => null]);
        DB::table('desa')->where('tgl_akses_hosting', '<', '0000-01-01 00:00:00')->update(['tgl_akses_hosting' => null]);
        DB::table('desa')->update(['created_at' => now()]);

        // hapus duplikat desa
        DB::raw("delete t1 from desa t1 inner join desa t2 where t1.id < t2.id and t1.kode_desa = t2.kode_desa");
    }
}
