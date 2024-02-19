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
                'id' => 8,
                'judul' => 'Akhir Backup',
                'key' => 'akhir_backup',
                'value' => '',
                'keterangan' => 'Tanggal terakhir backup ke cloud storage',
                'jenis' => 'date',
                'option' => '',
                'kategori' => 'setting'
            ],
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
