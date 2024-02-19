<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\PengaturanAplikasi;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
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
            'judul' => 'Abaikan domain opensid',
            'key' => 'abaikan_domain_opensid',
            'value' => 'devpremium.opendesa.id|devumum.opendesa.id|dev.opendesa.id|demosid.opendesa.id|beta.opendesa.id|berputar.opendesa.id|demo.opensid.my.id|demo.opensid.or.id|opensid.id|beta.opensid.my.id|berputar.opensid.my.id|beta.opensid.or.id|berputar.opensid.or.id|opensid.my.id|sistemdesa.sunshinecommunity.id',
            'keterangan' => 'Daftar domain yang diabaikan',
            'jenis' => 'select-tag',
            'option' => '',
            'kategori' => 'setting'
        ]);

        PengaturanAplikasi::create([
            'judul' => 'Abaikan domain opendk',
            'key' => 'abaikan_domain_opendk',
            'value' => 'demodk.opendesa.id',
            'keterangan' => 'Daftar domain yang diabaikan',
            'jenis' => 'select-tag',
            'option' => '',
            'kategori' => 'setting'
        ]);

        Cache::forever('abaikan_domain_opensid', 'devpremium.opendesa.id|devumum.opendesa.id|dev.opendesa.id|demosid.opendesa.id|beta.opendesa.id|berputar.opendesa.id|demo.opensid.my.id|demo.opensid.or.id|opensid.id|beta.opensid.my.id|berputar.opensid.my.id|beta.opensid.or.id|berputar.opensid.or.id|opensid.my.id|sistemdesa.sunshinecommunity.id');
        Cache::forever('abaikan_domain_opendk', 'demodk.opendesa.id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        PengaturanAplikasi::where(['kategori' => 'setting'])->whereIn('key', ['abaikan_domain_opensid', 'abaikan_domain_opendk'])->delete();
    }
};
