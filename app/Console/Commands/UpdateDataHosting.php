<?php

namespace App\Console\Commands;

use App\Models\Desa;
use App\Models\LogUrlHosting;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class UpdateDataHosting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracksid:perbaikioffline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        foreach (Desa::whereNull('url_hosting')->get() as $value) { // cek web terdeteksi offline
            if (! is_local($value->url_lokal) && $value->url_lokal != null) {
                $url = fixDomainName($value->url_lokal);
                echo 'cek '.$url.PHP_EOL;

                //cek domain server
                try {
                    $response = Http::get('https://'.$url);
                    if ($response->status() == 200) {
                        echo 'true '.$url.PHP_EOL;
                        Desa::where('id', $value->id)->update(['url_hosting' => $value->url_lokal]);
                        LogUrlHosting::updateOrCreate(
                            [
                                'url' => $url,
                            ],
                            [
                                'url' => $url,
                                'status' => 200,
                            ]
                        );
                    }
                } catch (ClientException $e) {
                    $this->cek_http($url, $value->id);
                } catch (ConnectionException $e) {
                    $this->cek_http($url, $value->id);
                } catch (RequestException $e) {
                    $this->cek_http($url, $value->id);
                }
            }
        }
    }

    public function cek_http($url, $id)
    {
        try {
            $response = Http::get('http://'.$url);

            if ($response->status() == 200) {
                LogUrlHosting::updateOrCreate(
                    [
                        'url' => $url,
                    ],
                    [
                        'url' => $url,
                        'status' => 200,
                    ]
                );
            }
        } catch (ClientException $e) {
        } catch (ConnectionException $e) {
        } catch (RequestException $e) {
        }
    }
}
