<?php

namespace Tests\Feature\Api;

use App\Models\Akses;
use App\Models\Desa;
use App\Models\Notifikasi;
use App\Models\NotifikasiDesa;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class TrackControllerTest extends TracksidApiTest
{
    use DatabaseTransactions, WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable notifications for testing
        Notification::fake();
    }

    /** @test */
    public function can_track_desa_data_successfully()
    {
        $requestData = [
            'nama_desa' => 'Desa Testing',
            'kode_desa' => '11.01.01.2001',
            'kode_pos' => '23111',
            'nama_kecamatan' => 'Bakongan',
            'kode_kecamatan' => '11.01.01',
            'nama_kabupaten' => 'KAB ACEH SELATAN',
            'kode_kabupaten' => '11.01',
            'nama_provinsi' => 'ACEH',
            'kode_provinsi' => '11',
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
            'kode_desa' => '11.01.01.1001',
            'nama_desa' => 'Desa Testing',
            'nama_kecamatan' => 'Kecamatan Testing',
            'nama_kabupaten' => 'Kabupaten Testing',
            'nama_provinsi' => 'Provinsi Testing',
            'url_hosting' => 'https://testing.example.com',
            'versi_hosting' => '24.01.1',
        ]);

        // Verify access record was created
        $desa = Desa::where('kode_desa', '11.01.01.1001')->first();
        $this->assertDatabaseHas('akses', [
            'desa_id' => $desa->id,
            'url_referrer' => 'https://testing.example.com',
        ]);
    }

    /** @test */
    public function can_update_existing_desa_data()
    {
        // Get existing data from wilayah table (which has 91k+ records)
        $wilayah = DB::table('wilayah')->whereNotNull('kode')->first();
        $this->assertNotNull($wilayah, 'No wilayah data found');

        // Parse wilayah code to get hierarchical data
        // Format: XX.XX.XX.XXXX (provinsi.kabupaten.kecamatan.desa)
        $kode = $wilayah->kode;
        $parts = explode('.', $kode);
        
        $kode_provinsi = $parts[0] ?? '11';
        $kode_kabupaten = implode('.', array_slice($parts, 0, 2)) ?? '11.01';
        $kode_kecamatan = implode('.', array_slice($parts, 0, 3)) ?? '11.01.01';

        $requestData = [
            'nama_desa' => $wilayah->nama,
            'kode_desa' => $kode,
            'kode_pos' => '23112',
            'nama_kecamatan' => 'Kecamatan Testing',
            'kode_kecamatan' => $kode_kecamatan,
            'nama_kabupaten' => 'Kabupaten Testing',
            'kode_kabupaten' => $kode_kabupaten,
            'nama_provinsi' => 'Provinsi Testing',
            'kode_provinsi' => $kode_provinsi,
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
            'kode_desa' => $kode,
            'versi_hosting' => '24.02.0',
            'url_hosting' => 'https://baru.example.com',
        ]);
    }

    /** @test */
    public function creates_new_access_record_if_not_exists()
    {
        $requestData = [
            'nama_desa' => 'Desa Testing Access',
            'kode_desa' => '11.01.01.1003',
            'kode_pos' => '23113',
            'nama_kecamatan' => 'Kecamatan Testing',
            'kode_kecamatan' => '11.01.01',
            'nama_kabupaten' => 'Kabupaten Testing',
            'kode_kabupaten' => '11.01',
            'nama_provinsi' => 'Provinsi Testing',
            'kode_provinsi' => '11',
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://testing-access.example.com',
            'ip_address' => '192.168.1.102',
            'version' => '24.01.1',
        ];

        $response = $this->postJson('/api/track/desa', $requestData);

        $response->assertStatus(200);
        
        $desa = Desa::where('kode_desa', '11.01.01.1003')->first();
        
        // Verify access record was created
        $this->assertDatabaseHas('akses', [
            'desa_id' => $desa->id,
            'url_referrer' => 'https://testing-access.example.com',
            'client_ip' => $this->app['request']->ip(), // Default client IP
        ]);
    }

    /** @test */
    public function updates_existing_access_record_if_exists_today()
    {
        // Get existing data from wilayah table
        $wilayah = DB::table('wilayah')->whereNotNull('kode')->first();
        $this->assertNotNull($wilayah, 'No wilayah data found');

        // First, create a desa record using the wilayah data
        $desa = Desa::create([
            'nama_desa' => $wilayah->nama,
            'kode_desa' => $wilayah->kode,
            'nama_kecamatan' => 'Kecamatan Testing',
            'nama_kabupaten' => 'Kabupaten Testing',
            'nama_provinsi' => 'Provinsi Testing',
        ]);

        // Create access record for today
        Akses::create([
            'desa_id' => $desa->id,
            'tgl' => now(),
            'url_referrer' => 'https://old.example.com',
            'request_uri' => '/old-uri',
            'client_ip' => '192.168.1.103',
            'external_ip' => '192.168.1.103',
            'opensid_version' => '23.01.0',
        ]);

        $requestData = [
            'nama_desa' => $wilayah->nama,
            'kode_desa' => $wilayah->kode,
            'kode_pos' => '23114',
            'nama_kecamatan' => 'Kecamatan Testing',
            'kode_kecamatan' => '11.01.01',
            'nama_kabupaten' => 'Kabupaten Testing',
            'kode_kabupaten' => '11.01',
            'nama_provinsi' => 'Provinsi Testing',
            'kode_provinsi' => '11',
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://updated-access.example.com',
            'ip_address' => '192.168.1.104',
            'version' => '24.01.1',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(200);

        // Verify access record was updated (not created new)
        $accessRecord = Akses::where('desa_id', $desa->id)->whereDate('tgl', now())->first();
        $this->assertEquals(now()->format('Y-m-d'), $accessRecord->tgl->format('Y-m-d'));
    }

    /** @test */
    public function handles_notification_logic_correctly()
    {
        // Get existing data from wilayah table
        $wilayah = DB::table('wilayah')->whereNotNull('kode')->first();
        $this->assertNotNull($wilayah, 'No wilayah data found');

        // First, create a desa record using the wilayah data
        $desa = Desa::create([
            'nama_desa' => $wilayah->nama,
            'kode_desa' => $wilayah->kode,
            'nama_kecamatan' => 'Kecamatan Testing',
            'nama_kabupaten' => 'Kabupaten Testing',
            'nama_provinsi' => 'Provinsi Testing',
        ]);

        // Create notification for this desa
        $notification = \Database\Factories\NotifikasiFactory::new()->create();
        NotifikasiDesa::create([
            'id_desa' => $desa->id,
            'id_notifikasi' => $notification->id,
            'status' => 1, // Active
        ]);

        $requestData = [
            'nama_desa' => $wilayah->nama,
            'kode_desa' => $wilayah->kode,
            'kode_pos' => '23115',
            'nama_kecamatan' => 'Kecamatan Testing',
            'kode_kecamatan' => '11.01.01',
            'nama_kabupaten' => 'Kabupaten Testing',
            'kode_kabupaten' => '11.01',
            'nama_provinsi' => 'Provinsi Testing',
            'kode_provinsi' => '11',
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://testing-notif.example.com',
            'ip_address' => '192.168.1.105',
            'version' => '24.01.1',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(200);

        // Verify notification was returned in response
        $response->assertJsonStructure([]);

        // Verify notification was deactivated
        $this->assertDatabaseHas('notifikasi_desa', [
            'id_desa' => $desa->id,
            'id_notifikasi' => $notification->id,
            'status' => 0, // Should be inactive now
        ]);
    }

    /** @test */
    public function sends_telegram_notification_for_new_pemda_hosting_desa()
    {
        // Set up cache for telegram bot
        Cache::put('token_bot_telegram', 'test_bot_token', 3600);
        Cache::put('id_telegram', [123456789], 3600);

        $requestData = [
            'nama_desa' => 'Desa Testing Pemda',
            'kode_desa' => '11.01.01.1006',
            'kode_pos' => '23116',
            'nama_kecamatan' => 'Kecamatan Testing',
            'kode_kecamatan' => '11.01.01',
            'nama_kabupaten' => 'Kabupaten Testing',
            'kode_kabupaten' => '11.01',
            'nama_provinsi' => 'Provinsi Testing',
            'kode_provinsi' => '11',
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://testing.go.id', // .go.id domain indicates pemda hosting
            'ip_address' => '192.168.1.106',
            'version' => '24.01.1',
        ];

        $response = $this->postJsonWithToken('/api/track/desa', $requestData);

        $response->assertStatus(200);
        
        // Verify desa was created with .go.id domain
        $this->assertDatabaseHas('desa', [
            'kode_desa' => '11.01.01.1006',
            'url_hosting' => 'https://testing.go.id',
        ]);

        // Verify telegram notification was sent
        Notification::assertSentToTimes(
            ['Pantau Notifikasi'],
            'App\Notifications\InfoNotification',
            1
        );
    }

    /** @test */
    public function does_not_send_telegram_notification_if_no_cache_values()
    {
        // Clear cache to simulate no telegram settings
        Cache::forget('token_bot_telegram');
        Cache::forget('id_telegram');

        $requestData = [
            'nama_desa' => 'Desa Testing No Telegram',
            'kode_desa' => '11.01.01.1007',
            'kode_pos' => '23117',
            'nama_kecamatan' => 'Kecamatan Testing',
            'kode_kecamatan' => '11.01.01',
            'nama_kabupaten' => 'Kabupaten Testing',
            'kode_kabupaten' => '11.01',
            'nama_provinsi' => 'Provinsi Testing',
            'kode_provinsi' => '11',
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://testing2.go.id', // .go.id domain indicates pemda hosting
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
        $requestData = [
            // Missing required fields
            'nama_desa' => '', // Empty required field
            'kode_desa' => 'invalid-code', // Non-existing code
            'kode_pos' => '23118',
            'nama_kecamatan' => '',
            'kode_kecamatan' => 'invalid-kec',
            'nama_kabupaten' => '',
            'kode_kabupaten' => 'invalid-kab',
            'nama_provinsi' => '',
            'kode_provinsi' => 'invalid-prov',
            'alamat_kantor' => '',
            'url' => 'invalid-url', // Invalid URL format
            'ip_address' => '',
            'version' => '',
        ];

        $response = $this->postJson('/api/track/desa', $requestData);

        $response->assertStatus(422); // Validation error
        $response->assertJsonStructure(['message']); // Laravel validation error structure
    }

    /** @test */
    public function handles_database_transaction_rollback_on_error()
    {
        // Mock an exception scenario by attempting to insert invalid data
        $requestData = [
            'nama_desa' => str_repeat('x', 1000), // Too long for the field
            'kode_desa' => '11.01.01.1008',
            'kode_pos' => '23119',
            'nama_kecamatan' => 'Kecamatan Testing',
            'kode_kecamatan' => '11.01.01',
            'nama_kabupaten' => 'Kabupaten Testing',
            'kode_kabupaten' => '11.01',
            'nama_provinsi' => 'Provinsi Testing',
            'kode_provinsi' => '11',
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://error-test.example.com',
            'ip_address' => '192.168.1.108',
            'version' => '24.01.1',
        ];

        $response = $this->postJson('/api/track/desa', $requestData);

        $response->assertStatus(422);
        
        // Verify no data was persisted due to rollback
        $this->assertDatabaseMissing('desa', [
            'kode_desa' => '11.01.01.1008',
        ]);
    }

    /** @test */
    public function handles_local_vs_hosting_detection()
    {
        $requestData = [
            'nama_desa' => 'Desa Testing Lokal',
            'kode_desa' => '11.01.01.1009',
            'kode_pos' => '23120',
            'nama_kecamatan' => 'Kecamatan Testing',
            'kode_kecamatan' => '11.01.01',
            'nama_kabupaten' => 'Kabupaten Testing',
            'kode_kabupaten' => '11.01',
            'nama_provinsi' => 'Provinsi Testing',
            'kode_provinsi' => '11',
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'http://localhost:8000', // Local URL
            'ip_address' => '127.0.0.1', // Local IP
            'version' => '24.01.1',
        ];

        $response = $this->postJson('/api/track/desa', $requestData);

        $response->assertStatus(200);
        
        // Verify desa was created with local attributes
        $this->assertDatabaseHas('desa', [
            'kode_desa' => '11.01.01.1009',
            'url_lokal' => 'http://localhost:8000',
            'versi_lokal' => '24.01.1',
        ]);
    }

    /** @test */
    public function handles_contact_information()
    {
        $requestData = [
            'nama_desa' => 'Desa Testing Kontak',
            'kode_desa' => '11.01.01.1010',
            'kode_pos' => '23121',
            'nama_kecamatan' => 'Kecamatan Testing',
            'kode_kecamatan' => '11.01.01',
            'nama_kabupaten' => 'Kabupaten Testing',
            'kode_kabupaten' => '11.01',
            'nama_provinsi' => 'Provinsi Testing',
            'kode_provinsi' => '11',
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

        $response = $this->postJson('/api/track/desa', $requestData);

        $response->assertStatus(200);
        
        // Verify contact information was stored as JSON
        $desa = Desa::where('kode_desa', '11.01.01.1010')->first();
        $this->assertEquals([
            'nama' => 'John Doe',
            'hp' => '081234567890',
            'jabatan' => 'Kepala Desa',
        ], $desa->kontak);
    }

    /** @test */
    public function handles_theme_information()
    {
        $requestData = [
            'nama_desa' => 'Desa Testing Tema',
            'kode_desa' => '11.01.01.1011',
            'kode_pos' => '23122',
            'nama_kecamatan' => 'Kecamatan Testing',
            'kode_kecamatan' => '11.01.01',
            'nama_kabupaten' => 'Kabupaten Testing',
            'kode_kabupaten' => '11.01',
            'nama_provinsi' => 'Provinsi Testing',
            'kode_provinsi' => '11',
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://tema.example.com',
            'ip_address' => '192.168.1.110',
            'version' => '24.01.1',
            'tema' => 'Silir', // One of the PRO themes
        ];

        $response = $this->postJson('/api/track/desa', $requestData);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('desa', [
            'kode_desa' => '11.01.01.1011',
            'tema' => 'Silir',
        ]);
    }

    /** @test */
    public function handles_layanan_and_sebutan_desa()
    {
        $requestData = [
            'nama_desa' => 'Desa Testing Layanan',
            'kode_desa' => '11.01.01.1012',
            'kode_pos' => '23123',
            'nama_kecamatan' => 'Kecamatan Testing',
            'kode_kecamatan' => '11.01.01',
            'nama_kabupaten' => 'Kabupaten Testing',
            'kode_kabupaten' => '11.01',
            'nama_provinsi' => 'Provinsi Testing',
            'kode_provinsi' => '11',
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => 'https://layanan.example.com',
            'ip_address' => '192.168.1.111',
            'version' => '24.01.1',
            'sebutan_desa' => 'Desa',
            'layanan' => 'siappakai', // One of the service options
        ];

        $response = $this->postJson('/api/track/desa', $requestData);

        $response->assertStatus(200);
        
        // Verify layanan and sebutan_desa were stored
        $this->assertDatabaseHas('desa', [
            'kode_desa' => '11.01.01.1012',
            'sebutan_desa' => 'Desa',
            'layanan' => 'siappakai',
        ]);
    }
}