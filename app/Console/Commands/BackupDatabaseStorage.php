<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Spatie\DbDumper\Databases\MySql;

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

    private $folder_database;

    private $database_name = 'db_pantau.sql';

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
        $this->folder_database = folderBackupDatabase();
        $this->backupDatabase();
        $this->backupStorage();
    }

    private function backupDatabase()
    {
        try {
            $backup = MySql::create()
                ->setDbName(env('DB_DATABASE'))
                ->setUserName(env('DB_USERNAME'))
                ->setPassword(env('DB_PASSWORD'));

            $backup->dumpToFile($this->folder_database.'/'.$this->database_name);
        } catch (Exception $ex) {
            var_dump('Peringatan : gagal backup ke database Pantau, silakan cek koneksi !!!');

            return exec('rm '.$this->folder_database.'/'.$this->database_name);
        }
    }

    private function backupStorage()
    {
        $folderdesa_from = 'storage';
        $folderdesa_to = folder_backup().DIRECTORY_SEPARATOR.'storage';

        if (file_exists($folderdesa_from)) {
            exec('cp -R '.$folderdesa_from.' '.$folderdesa_to);
        }
    }
}
