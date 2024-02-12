<?php

use App\Models\PengaturanAplikasi;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pengaturan_aplikasi = [
            [
                'id' => 5,
                'judul' => 'Cloud Storage',
                'key' => 'cloud_storage',
                'value' => '0',
                'keterangan' => 'Pilih cloud storage yang akan digunakan untuk backup',
                'jenis' => 'select',
                'option' => json_encode(['0' => 'Tidak Memilih Cloud Storage' , '1' => 'Google Drive', '2' => 'VPS / SFTP']),
                'kategori' => 'setting'
            ],
            [
                'id' => 6,
                'judul' => 'Waktu Backup',
                'key' => 'waktu_backup',
                'value' => '7',
                'keterangan' => 'Berapa hari sekali untuk melakukan backup',
                'jenis' => 'number',
                'option' => '',
                'kategori' => 'setting'
            ],
            [
                'id' => 7,
                'judul' => 'Maksimal Backup',
                'key' => 'maksimal_backup',
                'value' => '9',
                'keterangan' => 'Berapa maksimal directory backup yang ada di cloud storage',
                'jenis' => 'number',
                'option' => '',
                'kategori' => 'setting'
            ]
        ];

        foreach($pengaturan_aplikasi as $item){
            PengaturanAplikasi::create($item);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
