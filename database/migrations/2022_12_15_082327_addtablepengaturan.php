<?php

use App\Models\PengaturanAplikasi;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('pengaturan_aplikasi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('judul', 100);
            $table->string('key', 100)->index();
            $table->text('value');
            $table->string('keterangan', 200);
            $table->string('jenis', 30);
            $table->longText('option');
            $table->string('kategori', 30);
            $table->timestamps();
        });

        PengaturanAplikasi::create([
            'id' => 1,
            'judul' => 'Tampilkan Url',
            'key' => 'show_url',
            'value' => 0,
            'keterangan' => 'Tampilkan Url Untuk Publik',
            'jenis' => 'select',
            'option' => json_encode(['0' => 'Sembunyikan' , '1' => 'Tampilkan']),
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
        Schema::dropIfExists('pengaturan_aplikasi');
    }
};
