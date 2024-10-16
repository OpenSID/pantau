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
    protected $signature = 'pantau:optimasi-desa';

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
        DB::table('desa as d')
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

        DB::table('desa')
            ->select([
                'id',
                'url_lokal',
                'ip_lokal',
                'versi_lokal',
                'tgl_akses_lokal',
            ])
            ->where('url_hosting', '<>', '')
            ->whereNotNull('url_hosting')
            ->whereRaw("(CASE WHEN ((url_lokal Like 'localhost%' || url_lokal Like '10.%' || url_lokal Like '127.%' || url_lokal Like '192.168.%' || url_lokal Like '169.254.%' || url_lokal REGEXP '(^172\.1[6-9]\.)|(^172\.2[0-9]\.)|(^172\.3[0-1]\.)')) THEN 1 ELSE 0 END) = 0")
            ->chunkById(100, function ($desa) {
                foreach ($desa as $d) {
                    DB::table('desa')
                        ->where('id', $d->id)
                        ->update([
                            'url_lokal' => null,
                            'ip_lokal' => null,
                            'versi_lokal' => null,
                            'tgl_akses_lokal' => null,
                        ]);
                }
            });

        changeLogPermissions('777');
    }
}
