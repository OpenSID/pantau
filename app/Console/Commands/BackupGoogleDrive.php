<?php

namespace App\Console\Commands;

use App\Http\Controllers\Helpers\RemoteController;
use App\Models\PengaturanAplikasi;
use Illuminate\Console\Command;

class BackupGoogleDrive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracksid:backup-google-drive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Melakukan backup database dan folder storage ke google drive';

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
        $storage_type = 'drive';

        // nama remote yang dibuat melalui rclone config
        $remote_name = 'backup-drive';

        if (PengaturanAplikasi::get_pengaturan()['cloud_storage'] == 1) {
            // update tanggal terakhir backup
            $this->remote->tanggalAkhirBackup();

            // data pelanggan
            $akhir_backup = PengaturanAplikasi::get_pengaturan()['akhir_backup'];

            // hapus data yang paling lama dengan batas maksimal yang ditentukan
            $this->remote->removeBackupCloudStorage($remote_name, $akhir_backup, null);

            // proses backup
            $this->remote->backupToCloudStorage($storage_type, $remote_name, $akhir_backup, null);
        }
    }
}
