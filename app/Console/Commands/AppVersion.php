<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AppVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracksid:app-version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek versi aplikasi Pantau';

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
        $this->info(pantau_versi());
    }
}
