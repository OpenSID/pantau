<?php

use App\Models\PengaturanAplikasi;
use Illuminate\Database\Migrations\Migration;
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
            'judul' => 'Wilayah Khusus',
            'key' => 'wilayah_khusus',
            'value' => '[{"key":"52","value":"Nusa Tenggara Barat"}]',
            'keterangan' => 'Daftar wilayah khusus yang dipantau',
            'jenis' => 'select-multiple',
            'option' => '[{"key": "11","value": "ACEH"},{"key": "12","value": "SUMATERA UTARA"},{"key": "13","value": "SUMATERA BARAT"},{"key": "14","value": "RIAU"},{"key": "15","value": "JAMBI"},{"key": "16","value": "SUMATERA SELATAN"},{"key": "17","value": "BENGKULU"},{"key": "18","value": "LAMPUNG"},{"key": "19","value": "KEPULAUAN BANGKA BELITUNG"},{"key": "21","value": "KEPULAUAN RIAU"},{"key": "31","value": "DKI Jakarta"},{"key": "32","value": "Jawa Barat"},{"key": "33","value": "Jawa Tengah"},{"key": "34","value": "DI Yogyakarta"},{"key": "35","value": "Jawa Timur"},{"key": "36","value": "Banten"},{"key": "51","value": "Bali"},{"key": "52","value": "Nusa Tenggara Barat"},{"key": "53","value": "Nusa Tenggara Timur"},{"key": "61","value": "Kalimantan Barat"},{"key": "62","value": "Kalimantan Tengah"},{"key": "63","value": "Kalimantan Selatan"},{"key": "64","value": "Kalimantan Timur"},{"key": "65","value": "Kalimantan Utara"},{"key": "71","value": "Sulawesi Utara"},{"key": "72","value": "Sulawesi Tengah"},{"key": "73","value": "Sulawesi Selatan"},{"key": "74","value": "Sulawesi Tenggara"},{"key": "75","value": "Gorontalo"},{"key": "76","value": "Sulawesi Barat"},{"key": "81","value": "Maluku"},{"key": "82","value": "Maluku Utara"},{"key": "91","value": "Papua"},{"key": "92","value": "Papua Barat"}]',
            'kategori' => 'setting'
        ]);

        Cache::forever('pantau_wilayah_khusus', ['52' => 'Nusa Tenggara Barat']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        PengaturanAplikasi::where(['kategori' => 'setting', 'key' => 'wilayah_khusus'])->delete();
    }
};
