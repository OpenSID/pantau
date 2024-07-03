<?php

namespace App\Http\Controllers\Helpers;

use Exception;
use App\Models\LogBackup;
use App\Models\PengaturanAplikasi;
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
    public function backupToCloudStorage($storage_type, $remote_name, $akhir_backup, $root)
    {
        try {
            if (file_exists(folder_backup()) && rclone_syncs_storage() == true && cek_tgl_akhir_backup($akhir_backup) == 0) {
                // nama app_url & tanggal backup
                $directory_backup = $root . $this->host . '/backupserverdukungan/' . date('Y-m-d');

                // membuat folder di storage_type
                exec('rclone mkdir ' . $remote_name . ':/' . $directory_backup);

                // proses backup ke storage_type
                exec('rclone -v sync ' . folder_backup() . ' ' . $remote_name . ':' . $directory_backup);

                // notif berhasil
                $this->command->notifMessage('Berhasil backup menggunakan tipe ' . $storage_type . ' tanggal ' . date('Y-m-d'));
            } else {
                // notif gagal
                $this->command->notifMessage('Gagal backup menggunakan tipe ' . $storage_type);
                LogBackup::create([
                    'status' => 0,
                    'log' => 'Gagal backup menggunakan tipe ' . $storage_type
                ]);
            }
        } catch (Exception $e) {
            LogBackup::create([
                'status' => 0,
                'log' => 'Gagal backup menggunakan tipe ' . $storage_type . '. Pesan error: '  . $e->getMessage()
            ]);
        }
    }

    public function removeBackupCloudStorage($remote_name, $akhir_backup, $root)
    {
        if ($this->countDirectoryCloudStorage($remote_name, $root) == max_backup_dir()) {
            $this->removeBackup(max_backup_dir(), $remote_name, $akhir_backup, $root);
        }
    }

    public function countDirectoryCloudStorage($remote_name, $root)
    {
        $count_dir = exec('rclone lsd ' . $remote_name . ':' . $root . $this->host . '/ | wc -l');
        $this->command->notifMessage('Jumlah Folder ' . $count_dir);

        return $count_dir;
    }

    public function removeBackup($directory, $remote_name, $akhir_backup, $root)
    {
        // folder yang paling lama
        $old_dir = exec('sudo rclone lsf ' . $remote_name . ':' . $root . $this->host . '/ | sort -r | tail -n +' . $directory);
        $old_dir = rtrim($old_dir, '/');

        // nama app_url & tanggal backup
        $directory_backup = $root . $this->host . '/backupserverdukungan/' . $old_dir;

        if (rclone_syncs_storage() == true && cek_tgl_akhir_backup($akhir_backup) == 0) {
            // hapus folder backup terlama
            exec('sudo rclone purge ' . $remote_name . ':/' . $directory_backup);
            /**
             * rclone rmdir = hapus directory
             * rclone rmdirs = hapus directory dengan beberapa directory di dalamnya
             * rclone purge = hapus semua directory dan file yang ada di directory yang akan dihapus
             * */

            // notif berhasil
            $this->command->notifMessage('Berhasil telah menghapus folder ' . $old_dir);
        } else {
            // notif gagal
            $this->command->notifMessage('Gagal hapus folder ' . $old_dir . ' tidak ada.');
        }
    }

    public function tanggalAkhirBackup()
    {
        $tanggal = PengaturanAplikasi::where('key', 'akhir_backup')->first();
        $tanggal->value = date('Y-m-d');
        $tanggal->save();
    }
}
