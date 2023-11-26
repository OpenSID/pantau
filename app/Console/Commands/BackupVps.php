<?php

namespace App\Console\Commands;

use App\Http\Controllers\Helpers\RemoteController;
use Illuminate\Console\Command;

class BackupVps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracksid:backup-vps-sftp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melakukan backup database dan folder storage ke VPS / SFTP';

    private $remote;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->remote = new RemoteController();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // tipe pencadangan, misalkan: drive, sftp, dll
        $storage_type = 'sftp';

        // nama remote yang dibuat melalui rclone config
        $remote_name = 'backup-sftp';

        // root untuk storage backup
        $root = env('ROOT_BACKUP').'storage'.DIRECTORY_SEPARATOR;

        if (cloud_storage() == 2 && cek_tgl_akhir_backup(tanggal_backup()) >= waktu_backup() && rclone_syncs_storage() == true) {
            // update tanggal terakhir backup
            $this->remote->tanggalAkhirBackup();

            // hapus data yang paling lama dengan batas maksimal yang ditentukan
            $this->remote->removeBackupCloudStorage($remote_name, tanggal_backup(), $root);

            // proses backup
            $this->remote->backupToCloudStorage($storage_type, $remote_name, tanggal_backup(), $root);
        }
    }
}
