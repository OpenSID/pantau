<?php

namespace App\Console\Commands;

use App\Models\TblBpsKemendagri;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class bps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bps:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbarui Kode Relasi BPS dengan Kemendagri';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = 'https://sig.bps.go.id/rest-bridging/getwilayah';
        $provinsi = Http::retry(3, 4000)->get($url, [
            'level' => 'provinsi',
            'parent' => 0,
        ]);

        foreach (json_decode($provinsi->body()) as $v_provinsi) {
            $kabupaten = Http::retry(3, 4000)->get($url, [
                'level' => 'kabupaten',
                'parent' => $v_provinsi->kode_bps,
            ]);

            foreach (json_decode($kabupaten->body()) as $v_kabupaten) {
                $kecamatan = Http::retry(3, 4000)->get($url, [
                    'level' => 'kecamatan',
                    'parent' => $v_kabupaten->kode_bps,
                ]);
                foreach (json_decode($kecamatan->body()) as $v_kecamatan) {
                    $desa = Http::retry(3, 4000)->get($url, [
                        'level' => 'desa',
                        'parent' => $v_kecamatan->kode_bps,
                    ]);
                    foreach (json_decode($desa->body()) as $v_desa) {
                        echo "Provinsi :  {$v_provinsi->nama_dagri} - Kabupaten : {$v_kabupaten->nama_dagri} - Kecamatan : {$v_kecamatan->nama_dagri} - Desa : {$v_desa->nama_dagri}".PHP_EOL;

                        TblBpsKemendagri::updateOrCreate([
                            'kode_desa_bps' => $v_desa->kode_bps,
                        ], [
                            'kode_provinsi_kemendagri' => $v_provinsi->kode_dagri,
                            'nama_provinsi_kemendagri' => $v_provinsi->nama_dagri,
                            'kode_provinsi_bps' => $v_provinsi->kode_bps,
                            'nama_provinsi_bps' => $v_provinsi->nama_bps,
                            'kode_kabupaten_kemendagri' => $v_kabupaten->kode_dagri,
                            'nama_kabupaten_kemendagri' => $v_kabupaten->nama_dagri,
                            'kode_kabupaten_bps' => $v_kabupaten->kode_bps,
                            'nama_kabupaten_bps' => $v_kabupaten->nama_bps,
                            'kode_kecamatan_kemendagri' => $v_kecamatan->kode_dagri,
                            'nama_kecamatan_kemendagri' => $v_kecamatan->nama_dagri,
                            'kode_kecamatan_bps' => $v_kecamatan->kode_bps,
                            'nama_kecamatan_bps' => $v_kecamatan->nama_bps,
                            'kode_desa_kemendagri' => $v_desa->kode_dagri,
                            'nama_desa_kemendagri' => $v_desa->nama_dagri,
                            'kode_desa_bps' => $v_desa->kode_bps,
                            'nama_desa_bps' => $v_desa->nama_bps,
                        ]);
                    }
                }
            }
        }
    }
}
