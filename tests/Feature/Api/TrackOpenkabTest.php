<?php

namespace Tests\Feature\Api;

use App\Models\Wilayah;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Cache as FacadesCache;
use Tests\TestCase;

class TrackOpenkabTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    /** @test */
    public function can_track_openkab_data_successfully()
    {
        $kodeWilayah = Wilayah::inRandomOrder()->first();
        $requestData = [
            'kode_kab' => $kodeWilayah->kode_kab,
            'nama_kab' => $kodeWilayah->nama_kab,
            'kode_prov' => $kodeWilayah->kode_prov,
            'nama_prov' => $kodeWilayah->nama_prov,
            'nama_aplikasi' => 'OpenKab Updated',
            'sebutan_kab' => 'Kabupaten',
            'url' => 'https://updated.example.com',
            'versi' => '2.0.0',
            'jumlah_desa' => 120,
            'jumlah_penduduk' => 55000,
            'jumlah_keluarga' => 13000,
            'jumlah_rtm' => 12000,
            'jumlah_bantuan' => 5500,
        ];

        $response = $this->postJson('/api/track/openkab', $requestData);

        $response->assertStatus(200)
                 ->assertJson(['status' => true]);

        // Verify data was updated in database
        $this->assertDatabaseHas('openkab', [
            'kode_kab' => $kodeWilayah->kode_kab,
            'versi' => '2.0.0',
            'jumlah_desa' => 120,
        ]);
    }

    public function can_track_openkab_when_chace_clear_data_successfully()
    {
        FacadesCache::forget('abaikan_domain_openkab');
        $kodeWilayah = Wilayah::inRandomOrder()->first();
        $requestData = [
            'kode_kab' => $kodeWilayah->kode_kab,
            'nama_kab' => $kodeWilayah->nama_kab,
            'kode_prov' => $kodeWilayah->kode_prov,
            'nama_prov' => $kodeWilayah->nama_prov,
            'nama_aplikasi' => 'OpenKab Updated',
            'sebutan_kab' => 'Kabupaten',
            'url' => 'https://updated.example.com',
            'versi' => '2.0.0',
            'jumlah_desa' => 120,
            'jumlah_penduduk' => 55000,
            'jumlah_keluarga' => 13000,
            'jumlah_rtm' => 12000,
            'jumlah_bantuan' => 5500,
        ];

        $response = $this->postJson('/api/track/openkab', $requestData);

        $response->assertStatus(200)
                 ->assertJson(['status' => true]);

        // Verify data was updated in database
        $this->assertDatabaseHas('openkab', [
            'kode_kab' => $kodeWilayah->kode_kab,
            'versi' => '2.0.0',
            'jumlah_desa' => 120,
        ]);
    }
}
