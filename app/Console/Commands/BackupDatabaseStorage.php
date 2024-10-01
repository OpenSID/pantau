<?php

namespace App\Console\Commands;

use App\Http\Controllers\Helpers\CommandController;
use App\Models\LogBackup;
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
    protected $signature = 'pantau:backup-database-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melakukan Backup Database dan folder storage (dapat dilakukan melaluo cronjob)';

    private $command;

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
        $this->command = new CommandController();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (cek_tgl_akhir_backup(tanggal_backup()) >= waktu_backup()) {
            $this->folder_database = folderBackupDatabase();
            $this->backupDatabase();
            $this->backupStorage();
        }
    }

    private function backupDatabase()
    {
        try {
            $backup = MySql::create()
                ->setDbName(env('DB_DATABASE'))
                ->setHost(env('DB_HOST', '127.0.0.1'))
                ->setPort(env('DB_PORT', 3306))
                ->setUserName(env('DB_USERNAME'))
                ->setPassword(env('DB_PASSWORD'));

            $backup->dumpToFile($this->folder_database.'/'.$this->database_name);

            LogBackup::create([
                'status' => 1,
                'log' => 'Success backup pantau:backup-database-storage',
            ]);
        } catch (Exception $ex) {
            $this->command->notifMessage('Peringatan : gagal backup ke database Pantau, silakan cek koneksi !!!');
            LogBackup::create([
                'status' => 0,
                'log' => 'pantau:backup-database-storage :'.$ex->getMessage(),
            ]);

            return exec('rm '.$this->folder_database.'/'.$this->database_name);
        }
    }

    private function backupStorage()
    {
        $folderdesa_from = 'storage'.DIRECTORY_SEPARATOR.'app';
        $folderdesa_to = folder_backup().DIRECTORY_SEPARATOR.'storage';

        if (! file_exists($folderdesa_to)) {
            mkdir($folderdesa_to, 0755, true);
        }

        if (file_exists($folderdesa_from)) {
            exec('cp -R '.$folderdesa_from.' '.$folderdesa_to);
        }
    }
}
