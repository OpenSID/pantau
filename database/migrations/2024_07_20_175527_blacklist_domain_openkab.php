<?php

use App\Models\PengaturanAplikasi;
use Illuminate\Support\Facades\Cache;
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
        PengaturanAplikasi::create([
            'judul' => 'Abaikan domain openkab',
            'key' => 'abaikan_domain_openkab',
            'value' => 'devopenkab.opendesa.id',
            'keterangan' => 'Daftar domain yang diabaikan',
            'jenis' => 'select-tag',
            'option' => '',
            'kategori' => 'setting'
        ]);

        Cache::forever('abaikan_domain_openkab', 'devopenkab.opendesa.id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        PengaturanAplikasi::where(['kategori' => 'setting'])->whereIn('key', ['abaikan_domain_openkab'])->delete();
    }
};
