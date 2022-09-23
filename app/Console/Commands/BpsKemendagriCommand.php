<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BpsKemendagriCommand extends Command
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
    protected $signature = 'tracksid:sinkronasi-bps-kemendagri';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronasi Kode Relasi BPS dengan Kemendagri';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->provinsi = collect(json_decode((new Client())->get("{$this->url}getwilayah")->getBody()->getContents(), true));

            collect($this->provinsi)->map(function ($item) {
                return [
                    'kode_provinsi_kemendagri' => $item['kode_dagri'],
                    'nama_provinsi_kemendagri' => $item['nama_dagri'],
                    'kode_provinsi_bps' => $item['kode_bps'],
                    'nama_provinsi_bps' => $item['nama_bps'],
                ];
            })
            ->chunk(10)
            ->each(function ($chunk) {
                DB::table('bps_kemendagri_provinsi')->upsert($chunk->all(), 'kode_provinsi_bps');
            });

            DB::table('bps_kemendagri_provinsi')->update(['created_at' => now(), 'updated_at' => now()]);

            $this->requests($this->provinsi, 'kabupaten');
            $this->requests($this->kabupaten, 'kecamatan');
            $this->requests($this->kecamatan, 'desa');
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
            'concurrency' => 50,
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

        collect($this->{$level})->map(function ($item) use ($level) {
            return [
                "kode_{$level}_kemendagri" => $item['kode_dagri'],
                "nama_{$level}_kemendagri" => $item['nama_dagri'],
                "kode_{$level}_bps" => $item['kode_bps'],
                "nama_{$level}_bps" => $item['nama_bps'],
            ];
        })
        ->chunk(1000)
        ->each(function ($chunk) use ($level) {
            DB::table("bps_kemendagri_{$level}")->upsert($chunk->all(), "kode_{$level}_bps");
        });

        DB::table("bps_kemendagri_{$level}")->update(['created_at' => now(), 'updated_at' => now()]);

        $this->output->progressFinish();
    }
}
