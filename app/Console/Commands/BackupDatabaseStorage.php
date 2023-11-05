<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabaseStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracksid:backup-database-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melakukan Backup Database dan folder storage (dapat dilakukan melaluo cronjob)';

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
        return 0;
    }
}
