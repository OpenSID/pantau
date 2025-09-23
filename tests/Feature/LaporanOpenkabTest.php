<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Openkab;

class LaporanOpenkabTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_laporan_openkab_route_exists()
    {
        $response = $this->actingAs($this->user)
            ->get('/laporan/openkab');

        // Test that the route doesn't return 404
        $this->assertNotEquals(404, $response->status());
    }

    public function test_laporan_openkab_ajax_request_works()
    {
        // Create test data using factory
        Openkab::factory()->create([
            'kode_kab' => '3201',
            'nama_kab' => 'Bogor',
            'kode_prov' => '32',
            'nama_prov' => 'Jawa Barat',
            'versi' => '23.01',
        ]);

        $response = $this->actingAs($this->user)
            ->ajaxGet('/laporan/openkab');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data'
        ]);
    }

    public function test_laporan_openkab_is_publicly_accessible()
    {
        // Test that the route is publicly accessible (no auth required)
        $response = $this->get('/laporan/openkab');

        // Should return 200, not redirect to login
        $this->assertNotEquals(404, $response->status());
        $this->assertNotEquals(302, $response->status()); // Not a redirect
    }

    public function test_laporan_openkab_statistics_display()
    {
        // Create test data
        Openkab::factory()->create([
            'kode_kab' => '3201',
            'nama_kab' => 'Bogor',
            'kode_prov' => '32',
            'nama_prov' => 'Jawa Barat',
            'versi' => '23.01',
        ]);

        Openkab::factory()->create([
            'kode_kab' => '3301',
            'nama_kab' => 'Cilacap',
            'kode_prov' => '33',
            'nama_prov' => 'Jawa Tengah',
            'versi' => '', // Empty version
        ]);

        $response = $this->actingAs($this->user)
            ->get('/laporan/openkab');

        $response->assertStatus(200);
        $response->assertViewHas(['jumlahProvinsi', 'totalKabupaten', 'kabupatenTerpasang']);
    }

    public function test_laporan_openkab_controller_method_exists()
    {
        $this->assertTrue(method_exists('App\Http\Controllers\LaporanOpenkabController', 'index'));
    }

    public function test_laporan_openkab_data_structure()
    {
        // Create test data
        Openkab::factory()->create([
            'kode_kab' => '3201',
            'nama_kab' => 'Bogor',
            'kode_prov' => '32',
            'nama_prov' => 'Jawa Barat',
            'versi' => '23.01',
            'url' => 'https://bogor.go.id',
        ]);

        $response = $this->actingAs($this->user)
            ->ajaxGet('/laporan/openkab');

        $response->assertStatus(200);

        $data = $response->json();

        // Check if data contains expected structure
        $this->assertArrayHasKey('data', $data);
        $this->assertGreaterThan(0, count($data['data']));

        // Check first row structure
        $firstRow = $data['data'][0];
        $this->assertArrayHasKey('tgl_rekam', $firstRow);
        $this->assertArrayHasKey('nama_kab', $firstRow);
        $this->assertArrayHasKey('nama_prov', $firstRow);
        $this->assertArrayHasKey('url', $firstRow);
        $this->assertArrayHasKey('versi', $firstRow);
    }

    public function test_laporan_openkab_filter_provinsi()
    {
        // Create test data
        Openkab::factory()->create([
            'kode_kab' => '3201',
            'nama_kab' => 'Bogor',
            'kode_prov' => '32',
            'nama_prov' => 'Jawa Barat',
            'versi' => '23.01',
        ]);

        Openkab::factory()->create([
            'kode_kab' => '3202',
            'nama_kab' => 'Sukabumi',
            'kode_prov' => '32',
            'nama_prov' => 'Jawa Barat',
            'versi' => '23.02',
        ]);

        $response = $this->actingAs($this->user)
            ->ajaxGet('/laporan/openkab?filter=provinsi');

        $response->assertStatus(200);

        $data = $response->json();

        // Should group by province, so should have only 1 record (Jawa Barat)
        $this->assertCount(1, $data['data']);
        $this->assertEquals(2, $data['data'][0]['jumlah_kabupaten']);
    }

    public function test_laporan_openkab_filter_terpasang()
    {
        // Create test data with versions
        Openkab::factory()->create([
            'kode_kab' => '3201',
            'nama_kab' => 'Bogor',
            'versi' => '23.01',
        ]);

        // Create data without version (empty string)
        Openkab::factory()->create([
            'kode_kab' => '3202',
            'nama_kab' => 'Sukabumi',
            'versi' => '',
        ]);

        $response = $this->actingAs($this->user)
            ->ajaxGet('/laporan/openkab?filter=terpasang');

        $response->assertStatus(200);

        $data = $response->json();

        // Should only show kabupaten with versions installed
        $this->assertCount(1, $data['data']);
    }

    public function test_laporan_openkab_filter_total()
    {
        // Create test data
        Openkab::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->ajaxGet('/laporan/openkab?filter=total');

        $response->assertStatus(200);

        $data = $response->json();

        // Should show all kabupaten
        $this->assertCount(3, $data['data']);
    }    /**
         * Helper method to make AJAX requests
         */
    protected function ajaxGet($url)
    {
        return $this->get($url, [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
    }
}
