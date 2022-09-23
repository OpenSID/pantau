<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DesaOptimasiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracksid:optimasi-desa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimasi table desa sesuai Wilayah Administratif (Permendagri No. 77 Tahun 2019)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $result = DB::table('desa as d')
            ->leftJoin('kode_wilayah as k', 'd.kode_desa', 'k.kode_desa')
            ->whereNotNull('d.kode_desa')
            ->update([
                'd.nama_desa' => DB::raw('k.nama_desa'),
                'd.kode_desa' => DB::raw('k.kode_desa'),
                'd.nama_kecamatan' => DB::raw('k.nama_kec'),
                'd.kode_kecamatan' => DB::raw('k.kode_kec'),
                'd.nama_kabupaten' => DB::raw('k.nama_kab'),
                'd.kode_kabupaten' => DB::raw('k.kode_kab'),
                'd.nama_provinsi' => DB::raw('k.nama_prov'),
                'd.kode_provinsi' => DB::raw('k.kode_prov'),
            ]);

        $this->info("Jumlah desa yang berhasil di optimasi: {$result}");
    }
}
