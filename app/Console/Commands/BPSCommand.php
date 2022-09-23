<?php

namespace App\Console\Commands;

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use App\Models\TblBpsKemendagri;
use Illuminate\Support\Collection;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class BPSCommand extends Command
{
    /**
     * @var string
     */
    protected $url = 'https://sig.bps.go.id/rest-bridging/';

    /**
     * @var Collection
     */
    protected $provinsi;

    /**
     * @var Collection
     */
    protected $kabupaten;

    /**
     * @var Collection
     */
    protected $kecamatan;

    /**
     * @var Collection
     */
    protected $desa;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracksid:sync-bps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbarui Kode Relasi BPS dengan Kemendagri';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->provinsi = collect(json_decode((new Client())->get("{$this->url}getwilayah")->getBody()->getContents(), true));

            $this->requests($this->provinsi, 'kabupaten');
            $this->requests($this->kabupaten, 'kecamatan');
            $this->requests($this->kecamatan, 'desa');

            $bar = $this->output->createProgressBar(count($this->desa));
            $bar->start();

            collect($this->desa)->map(function ($desa) use ($bar) {
                $prov = collect($this->provinsi)->where('kode_dagri', Str::substrReplace($desa['kode_dagri'], '', -11));
                $kab = collect($this->kabupaten)->where('kode_dagri', Str::substrReplace($desa['kode_dagri'], '', -8));
                $kec = collect($this->kecamatan)->where('kode_dagri', Str::substrReplace($desa['kode_dagri'], '', -5));

                $bar->advance();

                return [
                    'kode_provinsi_kemendagri' => $prov->pluck('kode_dagri')->first(),
                    'nama_provinsi_kemendagri' => $prov->pluck('nama_dagri')->first(),
                    'kode_provinsi_bps' => $prov->pluck('kode_bps')->first(),
                    'nama_provinsi_bps' => $prov->pluck('nama_bps')->first(),

                    'kode_kabupaten_kemendagri' => $kab->pluck('kode_dagri')->first(),
                    'nama_kabupaten_kemendagri' => $kab->pluck('nama_dagri')->first(),
                    'kode_kabupaten_bps' => $kab->pluck('kode_bps')->first(),
                    'nama_kabupaten_bps' => $kab->pluck('nama_bps')->first(),

                    'kode_kecamatan_kemendagri' => $kec->pluck('kode_dagri')->first(),
                    'nama_kecamatan_kemendagri' => $kec->pluck('nama_dagri')->first(),
                    'kode_kecamatan_bps' => $kec->pluck('kode_bps')->first(),
                    'nama_kecamatan_bps' => $kec->pluck('nama_bps')->first(),

                    'kode_desa_kemendagri' => $desa['kode_dagri'],
                    'nama_desa_kemendagri' => $desa['nama_dagri'],
                    'kode_desa_bps' => $desa['kode_bps'],
                    'nama_desa_bps' => $desa['nama_bps'],
                ];
            })
            ->chunk(1000)
            ->each(function ($chunk) {
                TblBpsKemendagri::upsert($chunk->all(), 'kode_desa_bps');
            });

            $bar->finish();
        } catch (ClientException $e) {
            report($e);
        }
    }

    protected function requests($data, $level = '')
    {
        $client = new Client(['base_uri' => $this->url]);

        $requests = function () use ($client, $data, $level) {
            $this->output->progressStart(count($data));
            foreach ($data as $value) {
                yield function () use ($client, $value, $level) {
                    return $client->getAsync("getwilayah?level={$level}&parent={$value['kode_bps']}");
                };
            }
        };

        $pool = new Pool($client, $requests(), [
            'concurrency' => 10,
            'fulfilled' => function (Response $response, $index) use ($level) {
                $this->{$level}[] = json_decode($response->getBody()->getContents(), true);
                $this->output->progressAdvance();
            },
            'rejected' => function (RequestException $reason, $index) {
                report($reason);
            },
        ]);

        // Initiate the transfers and create a promise
        $promise = $pool->promise();
        // Force the pool of requests to complete
        $promise->wait();

        $this->{$level} = collect($this->{$level})->flatten(1)->all();
        $this->output->progressFinish();
    }
}
