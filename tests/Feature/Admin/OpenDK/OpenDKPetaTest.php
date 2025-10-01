<?php

namespace Tests\Feature\Admin\OpenDK;

use App\Models\User;
use App\Models\Opendk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OpenDKPetaTest extends TestCase
{
    use WithFaker;

    /**
     * Test halaman peta OpenDK dapat diakses oleh user yang terautentikasi
     */
    public function test_halaman_peta_opendk_dapat_diakses_dengan_autentikasi()
    {
        // Buat user untuk testing (tanpa RefreshDatabase agar tidak merusak data)
        $user = User::factory()->make([
            'email' => 'test_opendk_peta@example.com',
            'name' => 'Test User OpenDK Peta'
        ]);

        $response = $this->actingAs($user)->get('/opendk/peta');

        $response->assertStatus(200);
        $response->assertSeeText('Peta Sebaran OpenDK');
        $response->assertSeeText('Sebaran pengguna OpenDK di Indonesia');
        $response->assertSeeText('Provinsi');
        $response->assertSeeText('Kabupaten');
        $response->assertSeeText('Kecamatan');
        $response->assertSeeText('Periode');
    }

    /**
     * Test halaman peta memuat komponen peta yang diperlukan
     */
    public function test_halaman_peta_memuat_komponen_leaflet()
    {
        $user = User::factory()->make([
            'email' => 'test_opendk_leaflet@example.com',
            'name' => 'Test User Leaflet'
        ]);

        $response = $this->actingAs($user)->get('/opendk/peta');

        $response->assertStatus(200);
        // Pastikan div map ada
        $response->assertSee('<div id="map"></div>', false);
        // Pastikan script leaflet dipanggil (tanpa HTML encoding)
        $response->assertSee("L.map('map'", false);
        $response->assertSee('L.tileLayer', false);
        $response->assertSee('leaflet', false);
    }

    /**
     * Test API endpoint untuk data peta mengembalikan format GeoJSON
     */
    public function test_api_peta_mengembalikan_geojson()
    {
        $user = User::factory()->make([
            'email' => 'test_opendk_api@example.com',
            'name' => 'Test User API'
        ]);

        $response = $this->actingAs($user)
            ->getJson('/opendk/peta', [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'type',
            'features' => [
                '*' => [
                    'type',
                    'geometry' => [
                        'type',
                        'coordinates'
                    ],
                    'properties',
                    'id'
                ]
            ]
        ]);

        // Pastikan type adalah FeatureCollection
        $response->assertJson([
            'type' => 'FeatureCollection'
        ]);
    }

    /**
     * Test filter provinsi berfungsi pada API
     */
    public function test_filter_provinsi_berfungsi()
    {
        $user = User::factory()->make([
            'email' => 'test_opendk_filter@example.com',
            'name' => 'Test User Filter'
        ]);

        $response = $this->actingAs($user)
            ->getJson('/opendk/peta?kode_provinsi=32', [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'type',
            'features'
        ]);
    }

    /**
     * Test halaman memerlukan autentikasi
     */
    public function test_halaman_peta_memerlukan_autentikasi()
    {
        $response = $this->get('/opendk/peta');

        // Harus redirect ke halaman login
        $response->assertRedirect('/login');
    }

    /**
     * Test halaman memuat filter yang benar
     */
    public function test_halaman_memuat_filter_yang_benar()
    {
        $user = User::factory()->make([
            'email' => 'test_opendk_filter_ui@example.com',
            'name' => 'Test User Filter UI'
        ]);

        $response = $this->actingAs($user)->get('/opendk/peta');

        $response->assertStatus(200);

        // Test elemen filter
        $response->assertSee('name="provinsi"', false);
        $response->assertSee('name="kabupaten"', false);
        $response->assertSee('name="kecamatan"', false);
        $response->assertSee('name="periode"', false);

        // Test tombol filter
        $response->assertSee('id="filter"', false);
        $response->assertSee('id="reset"', false);

        // Test periode dropdown options
        $response->assertSeeText('Semua Periode');
        $response->assertSeeText('30 Hari Terakhir');
        $response->assertSeeText('3 Bulan Terakhir');
    }

    /**
     * Test JavaScript function loadData terdefinisi
     */
    public function test_javascript_load_data_terdefinisi()
    {
        $user = User::factory()->make([
            'email' => 'test_opendk_js@example.com',
            'name' => 'Test User JS'
        ]);

        $response = $this->actingAs($user)->get('/opendk/peta');

        $response->assertStatus(200);
        $response->assertSee('function loadData');
        $response->assertSee('L.markerClusterGroup');
        $response->assertSee('isValidCoordinate');
    }

    /**
     * Test route name dapat diakses
     */
    public function test_route_name_dapat_diakses()
    {
        $user = User::factory()->make([
            'email' => 'test_opendk_route@example.com',
            'name' => 'Test User Route'
        ]);

        $response = $this->actingAs($user)->get(route('admin.opendk.peta'));

        $response->assertStatus(200);
        $response->assertSeeText('Peta Sebaran OpenDK');
    }
}
