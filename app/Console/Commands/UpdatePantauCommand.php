<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdatePantauCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracksid:update-pantau';

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
        $path_root = dirname(base_path(), 1);
        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json',
            //'Authorization' => "token {$token}"
        ])->get('https://api.github.com/repos/OpenSID/tracksid/releases/latest')->throw()->json();
        $version_git = preg_replace('/[^0-9]/', '', $response['tag_name']);

        $content_versi = 0;
        if (file_exists($path_root . '/pantau/artisan')) {
            $content_versi = pantau_versi();
        }

        $version_server = preg_replace('/[^0-9]/', '', $content_versi);
        var_dump($version_server);
    }
}
