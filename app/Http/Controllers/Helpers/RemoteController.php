<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;

class RemoteController extends Controller
{
    private $command;
    private $host;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->command = new CommandController();
        $this->host = request()->getHttpHost();
    }

    /** proses remote rclone syncs to cloud storage */
    public function backupToCloudStorage($storage_type, $remote_name, $root){
        if (folder_backup() && rclone_syncs_storage() == true) {
            // nama app_url & tanggal backup
            $directory_backup = $root . $this->host . '/' . date('Y-m-d');

            // membuat folder di storage_type
            exec('rclone mkdir ' . $remote_name . ':/' . $directory_backup);

            // proses backup ke storage_type
            exec('rclone -v sync ' . folder_backup() . ' ' . $remote_name . ':' . $directory_backup);

            // notif berhasil
            $this->command->notifMessage('Berhasil backup menggunakan tipe ' . $storage_type. ' tanggal '. date('Y-m-d'));
        } else {
            // notif gagal
            $this->command->notifMessage('Gagal backup menggunakan tipe ' . $storage_type);
        }
    }
}
