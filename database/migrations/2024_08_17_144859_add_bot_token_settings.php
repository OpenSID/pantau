<?php

use App\Models\PengaturanAplikasi;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        PengaturanAplikasi::create([
            'judul' => 'Token Bot Telegram',
            'key'  => 'token_bot_telegram',
            'value' => '',
            'keterangan' => '',
            'jenis' => 'text',
            'kategori' => 'setting'
        ]);

        PengaturanAplikasi::create([
            'judul' => 'ID Telegram Sekretariat',
            'key'  => 'id_telegram',
            'value' => '',
            'keterangan' => '',
            'jenis' => 'select-tag',
            'kategori' => 'setting'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        PengaturanAplikasi::whereIn('key', ['token_bot_telegram', 'id_telegram'])->delete();
    }
};
