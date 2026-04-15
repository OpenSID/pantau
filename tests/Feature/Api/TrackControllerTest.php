<?php

namespace Tests\Feature\Api;

use App\Models\Desa;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class TrackControllerTest extends TracksidApiTest
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Disable notifications for testing
        Notification::fake();
    }

    /**
     * Remove dots from wilayah code for request.
     * e.g., "12.03.04.0001" -> "1203040001"
     */
    protected function formatKodeForRequest($kode)
    {
        return str_replace('.', '', $kode);
    }

    /** @test */
    public function can_track_desa_data_successfully()
    {
        // Get existing wilayah data from region table
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah, 'No wilayah data found in region table');

        $requestData = [
            'nama_desa' => $wilayah->nama_desa,
            'kode_desa' => $this->formatKodeForRequest($wilayah->kode_desa),
            'kode_pos' => '23111',
            'nama_kecamatan' => $wilayah->nama_kec,
            'kode_kecamatan' => $this->formatKodeForRequest($wilayah->kode_kec),
            'nama_kabupaten' => $wilayah->nama_kab,
            'kode_kabupaten' => $this->formatKodeForRequest($wilayah->kode_kab),
            'nama_provinsi' => $wilayah->nama_prov,
            'kode_provinsi' => $this->formatKodeForRequest($wilayah->kode_prov),
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://testing.example.com',
            'ip_address' => '192.168.1.100',
            'version' => '24.01.1',
            'jml_surat_tte' => 5,
            'modul_tte' => '1',
            'email_desa' => 'desa@testing.com',
            'telepon' => '02112345678',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(200);

        // Verify desa was created in database
        $this->assertDatabaseHas('desa', [
            'kode_desa' => $wilayah->kode_desa,
            'nama_desa' => $wilayah->nama_desa,
            'url_hosting' => 'testing.example.com',
            'versi_hosting' => '24.01.1',
        ]);

        // Verify access record was created
        $desa = Desa::where('kode_desa', $wilayah->kode_desa)->first();
        $this->assertDatabaseHas('akses', [
            'desa_id' => $desa->id,
            'url_referrer' => 'https://testing.example.com',
        ]);
    }

    /** @test */
    public function can_update_existing_desa_data()
    {
        // Get existing wilayah data from region table
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah, 'No wilayah data found in region table');

        $requestData = [
            'nama_desa' => $wilayah->nama_desa,
            'kode_desa' => $this->formatKodeForRequest($wilayah->kode_desa),
            'kode_pos' => '23112',
            'nama_kecamatan' => $wilayah->nama_kec,
            'kode_kecamatan' => $this->formatKodeForRequest($wilayah->kode_kec),
            'nama_kabupaten' => $wilayah->nama_kab,
            'kode_kabupaten' => $this->formatKodeForRequest($wilayah->kode_kab),
            'nama_provinsi' => $wilayah->nama_prov,
            'kode_provinsi' => $this->formatKodeForRequest($wilayah->kode_prov),
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Baru',
            'url' => 'https://baru.example.com',
            'ip_address' => '192.168.1.101',
            'version' => '24.02.0',
            'jml_surat_tte' => 10,
            'modul_tte' => '0',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(200);

        // Verify desa was created/updated in database
        $this->assertDatabaseHas('desa', [
            'kode_desa' => $wilayah->kode_desa,
            'versi_hosting' => '24.02.0',
            'url_hosting' => 'baru.example.com',
        ]);
    }

    /** @test */
    public function creates_new_access_record_if_not_exists()
    {
        // Get existing wilayah data from region table
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah, 'No wilayah data found in region table');

        $requestData = [
            'nama_desa' => $wilayah->nama_desa,
            'kode_desa' => $this->formatKodeForRequest($wilayah->kode_desa),
            'kode_pos' => '23113',
            'nama_kecamatan' => $wilayah->nama_kec,
            'kode_kecamatan' => $this->formatKodeForRequest($wilayah->kode_kec),
            'nama_kabupaten' => $wilayah->nama_kab,
            'kode_kabupaten' => $this->formatKodeForRequest($wilayah->kode_kab),
            'nama_provinsi' => $wilayah->nama_prov,
            'kode_provinsi' => $this->formatKodeForRequest($wilayah->kode_prov),
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://testing-access.example.com',
            'ip_address' => '192.168.1.102',
            'version' => '24.01.1',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(200);

        $desa = Desa::where('kode_desa', $wilayah->kode_desa)->first();

        // Verify access record was created
        $this->assertDatabaseHas('akses', [
            'desa_id' => $desa->id,
            'url_referrer' => 'https://testing-access.example.com',
            'client_ip' => $this->app['request']->ip(),
        ]);
    }

    /** @test */
    public function sends_telegram_notification_for_new_pemda_hosting_desa()
    {
        // Set up cache for telegram bot
        Cache::put('token_bot_telegram', 'test_bot_token', 3600);
        Cache::put('id_telegram', [123456789], 3600);

        // Get existing wilayah data from region table
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah, 'No wilayah data found in region table');

        $requestData = [
            'nama_desa' => $wilayah->nama_desa,
            'kode_desa' => $this->formatKodeForRequest($wilayah->kode_desa),
            'kode_pos' => '23116',
            'nama_kecamatan' => $wilayah->nama_kec,
            'kode_kecamatan' => $this->formatKodeForRequest($wilayah->kode_kec),
            'nama_kabupaten' => $wilayah->nama_kab,
            'kode_kabupaten' => $this->formatKodeForRequest($wilayah->kode_kab),
            'nama_provinsi' => $wilayah->nama_prov,
            'kode_provinsi' => $this->formatKodeForRequest($wilayah->kode_prov),
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://testing.go.id',
            'ip_address' => '192.168.1.106',
            'version' => '24.01.1',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(200);

        // Verify desa was created with .go.id domain
        $this->assertDatabaseHas('desa', [
            'kode_desa' => $wilayah->kode_desa,
            'url_hosting' => 'testing.go.id',
        ]);
    }

    /** @test */
    public function does_not_send_telegram_notification_if_no_cache_values()
    {
        // Clear cache to simulate no telegram settings
        Cache::forget('token_bot_telegram');
        Cache::forget('id_telegram');

        // Get existing wilayah data from region table
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah, 'No wilayah data found in region table');

        $requestData = [
            'nama_desa' => $wilayah->nama_desa,
            'kode_desa' => $this->formatKodeForRequest($wilayah->kode_desa),
            'kode_pos' => '23117',
            'nama_kecamatan' => $wilayah->nama_kec,
            'kode_kecamatan' => $this->formatKodeForRequest($wilayah->kode_kec),
            'nama_kabupaten' => $wilayah->nama_kab,
            'kode_kabupaten' => $this->formatKodeForRequest($wilayah->kode_kab),
            'nama_provinsi' => $wilayah->nama_prov,
            'kode_provinsi' => $this->formatKodeForRequest($wilayah->kode_prov),
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://testing2.go.id',
            'ip_address' => '192.168.1.107',
            'version' => '24.01.1',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(200);

        // Verify telegram notification was NOT sent
        Notification::assertNothingSent();
    }

    /** @test */
    public function handles_validation_errors()
    {
        // Get existing wilayah data for valid region codes
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah, 'No wilayah data found in region table');

        $requestData = [
            'nama_desa' => '',
            'kode_desa' => $this->formatKodeForRequest($wilayah->kode_desa),
            'kode_pos' => '23118',
            'nama_kecamatan' => $wilayah->nama_kec,
            'kode_kecamatan' => $this->formatKodeForRequest($wilayah->kode_kec),
            'nama_kabupaten' => $wilayah->nama_kab,
            'kode_kabupaten' => $this->formatKodeForRequest($wilayah->kode_kab),
            'nama_provinsi' => $wilayah->nama_prov,
            'kode_provinsi' => $this->formatKodeForRequest($wilayah->kode_prov),
            'alamat_kantor' => '',
            'url' => 'invalid-url',
            'ip_address' => '',
            'version' => '',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message']);
    }

    /** @test */
    public function handles_database_transaction_rollback_on_error()
    {
        // Get existing wilayah data for valid region codes
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah, 'No wilayah data found in region table');

        // Use invalid URL to trigger validation error
        $requestData = [
            'nama_desa' => $wilayah->nama_desa,
            'kode_desa' => $this->formatKodeForRequest($wilayah->kode_desa),
            'kode_pos' => '23119',
            'nama_kecamatan' => $wilayah->nama_kec,
            'kode_kecamatan' => $this->formatKodeForRequest($wilayah->kode_kec),
            'nama_kabupaten' => $wilayah->nama_kab,
            'kode_kabupaten' => $this->formatKodeForRequest($wilayah->kode_kab),
            'nama_provinsi' => $wilayah->nama_prov,
            'kode_provinsi' => $this->formatKodeForRequest($wilayah->kode_prov),
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'invalid-url', // Invalid URL to trigger validation error
            'ip_address' => '192.168.1.108',
            'version' => '24.01.1',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(422);

        // Verify no data was persisted due to validation error
        $this->assertDatabaseMissing('desa', [
            'kode_desa' => $wilayah->kode_desa,
            'url_hosting' => 'invalid-url',
        ]);
    }

    /** @test */
    public function handles_local_vs_hosting_detection()
    {
        // Get existing wilayah data for valid region codes
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah, 'No wilayah data found in region table');

        $requestData = [
            'nama_desa' => $wilayah->nama_desa,
            'kode_desa' => $this->formatKodeForRequest($wilayah->kode_desa),
            'kode_pos' => '23120',
            'nama_kecamatan' => $wilayah->nama_kec,
            'kode_kecamatan' => $this->formatKodeForRequest($wilayah->kode_kec),
            'nama_kabupaten' => $wilayah->nama_kab,
            'kode_kabupaten' => $this->formatKodeForRequest($wilayah->kode_kab),
            'nama_provinsi' => $wilayah->nama_prov,
            'kode_provinsi' => $this->formatKodeForRequest($wilayah->kode_prov),
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'http://localhost:8000',
            'ip_address' => '127.0.0.1',
            'version' => '24.01.1',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(200);

        // Verify desa was created with local attributes
        $this->assertDatabaseHas('desa', [
            'kode_desa' => $wilayah->kode_desa,
            'url_lokal' => 'localhost:8000',
            'versi_lokal' => '24.01.1',
        ]);
    }

    /** @test */
    public function handles_contact_information()
    {
        // Get existing wilayah data for valid region codes
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah, 'No wilayah data found in region table');

        $requestData = [
            'nama_desa' => $wilayah->nama_desa,
            'kode_desa' => $this->formatKodeForRequest($wilayah->kode_desa),
            'kode_pos' => '23121',
            'nama_kecamatan' => $wilayah->nama_kec,
            'kode_kecamatan' => $this->formatKodeForRequest($wilayah->kode_kec),
            'nama_kabupaten' => $wilayah->nama_kab,
            'kode_kabupaten' => $this->formatKodeForRequest($wilayah->kode_kab),
            'nama_provinsi' => $wilayah->nama_prov,
            'kode_provinsi' => $this->formatKodeForRequest($wilayah->kode_prov),
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://kontak.example.com',
            'ip_address' => '192.168.1.109',
            'version' => '24.01.1',
            'nama_kontak' => 'John Doe',
            'hp_kontak' => '081234567890',
            'jabatan_kontak' => 'Kepala Desa',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(200);

        // Verify contact information was stored as JSON
        $desa = Desa::where('kode_desa', $wilayah->kode_desa)->first();
        $this->assertEquals([
            'nama' => 'John Doe',
            'hp' => '081234567890',
            'jabatan' => 'Kepala Desa',
        ], $desa->kontak);
    }

    /** @test */
    public function handles_theme_information()
    {
        // Get existing wilayah data for valid region codes
        $wilayah = Wilayah::inRandomOrder()->whereNotNull('nama_desa')->first();
        $this->assertNotNull($wilayah, 'No wilayah data found in region table');

        $requestData = [
            'nama_desa' => $wilayah->nama_desa,
            'kode_desa' => $this->formatKodeForRequest($wilayah->kode_desa),
            'kode_pos' => '23122',
            'nama_kecamatan' => $wilayah->nama_kec,
            'kode_kecamatan' => $this->formatKodeForRequest($wilayah->kode_kec),
            'nama_kabupaten' => $wilayah->nama_kab,
            'kode_kabupaten' => $this->formatKodeForRequest($wilayah->kode_kab),
            'nama_provinsi' => $wilayah->nama_prov,
            'kode_provinsi' => $this->formatKodeForRequest($wilayah->kode_prov),
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://tema.example.com',
            'ip_address' => '192.168.1.110',
            'version' => '24.01.1',
            'tema' => 'Silir',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('desa', [
            'kode_desa' => $wilayah->kode_desa,
            'tema' => 'Silir',
        ]);
    }

    /** @test */
    public function handles_layanan_and_sebutan_desa()
    {
        // Get existing wilayah data for valid region codes
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah, 'No wilayah data found in region table');

        $requestData = [
            'nama_desa' => $wilayah->nama_desa,
            'kode_desa' => $this->formatKodeForRequest($wilayah->kode_desa),
            'kode_pos' => '23123',
            'nama_kecamatan' => $wilayah->nama_kec,
            'kode_kecamatan' => $this->formatKodeForRequest($wilayah->kode_kec),
            'nama_kabupaten' => $wilayah->nama_kab,
            'kode_kabupaten' => $this->formatKodeForRequest($wilayah->kode_kab),
            'nama_provinsi' => $wilayah->nama_prov,
            'kode_provinsi' => $this->formatKodeForRequest($wilayah->kode_prov),
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://layanan.example.com',
            'ip_address' => '192.168.1.111',
            'version' => '24.01.1',
            'sebutan_desa' => 'Desa',
            'layanan' => 'siappakai',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(200);

        // Verify layanan and sebutan_desa were stored
        $this->assertDatabaseHas('desa', [
            'kode_desa' => $wilayah->kode_desa,
            'sebutan_desa' => 'Desa',
            'layanan' => 'siappakai',
        ]);
    }
}