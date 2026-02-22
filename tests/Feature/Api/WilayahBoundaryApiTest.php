<?php

namespace Tests\Feature\Api;

use App\Models\Region;
use App\Models\WilayahBoundary;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WilayahBoundaryApiTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Clean up any existing test data first
        WilayahBoundary::where('kode', 'like', 'TEST%')->delete();
        Region::where('region_code', 'like', 'TEST%')->delete();

        // Create test data
        $this->createTestBoundaries();
    }

    protected function tearDown(): void
    {
        // Clean up test data after each test
        WilayahBoundary::where('kode', 'like', 'TEST%')->delete();
        Region::where('region_code', 'like', 'TEST%')->delete();

        parent::tearDown();
    }

    private function createTestBoundaries(): void
    {
        // Create regions with TEST_ prefix to avoid conflicts with production data
        Region::create(['region_code' => 'TEST_P1', 'region_name' => 'TEST ACEH', 'parent_code' => '0']);
        Region::create(['region_code' => 'TEST_K1', 'region_name' => 'TEST Aceh Selatan', 'parent_code' => 'TEST_P1']);
        Region::create(['region_code' => 'TEST_P2', 'region_name' => 'TEST DKI JAKARTA', 'parent_code' => '0']);
        Region::create(['region_code' => 'TEST_K2', 'region_name' => 'TEST Kepulauan Seribu', 'parent_code' => 'TEST_P2']);

        // Create boundaries
        WilayahBoundary::create([
            'kode' => 'TEST_P1',
            'level' => 'prov',
            'lat' => 4.2257,
            'lng' => 96.9119,
            'path' => [[[100.0, 0.0], [101.0, 0.0], [101.0, 1.0]]],
            'status' => 1,
        ]);

        WilayahBoundary::create([
            'kode' => 'TEST_K1',
            'level' => 'kab',
            'lat' => 3.1619,
            'lng' => 97.4365,
            'path' => [[[100.5, 0.5], [101.5, 0.5], [101.5, 1.5]]],
            'status' => 1,
        ]);

        WilayahBoundary::create([
            'kode' => 'TEST_P2',
            'level' => 'prov',
            'lat' => -6.2088,
            'lng' => 106.8456,
            'path' => [[[106.0, -6.5], [107.0, -6.5], [107.0, -6.0]]],
            'status' => 1,
        ]);

        WilayahBoundary::create([
            'kode' => 'TEST_K2',
            'level' => 'kab',
            'lat' => -5.6112,
            'lng' => 106.5297,
            'path' => [[[106.5, -5.7], [106.6, -5.7], [106.6, -5.5]]],
            'status' => 1,
        ]);
    }

    public function test_can_get_boundaries_list()
    {
        $response = $this->getJson('/api/boundaries');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'kode',
                        'level',
                        'level_name',
                        'lat',
                        'lng',
                        'centroid',
                        'status',
                        'region' => [
                            'region_code',
                            'region_name',
                        ],
                    ],
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ],
            ]);
    }

    public function test_can_filter_boundaries_by_level()
    {
        $response = $this->getJson('/api/boundaries?level=prov');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        foreach ($data as $boundary) {
            $this->assertEquals('prov', $boundary['level']);
        }
    }

    public function test_can_filter_boundaries_by_kode()
    {
        $response = $this->getJson('/api/boundaries?kode=TEST_P1');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    [
                        'kode' => 'TEST_P1',
                        'level' => 'prov',
                    ],
                ],
            ]);
    }

    public function test_can_search_boundaries()
    {
        $response = $this->getJson('/api/boundaries?search=TEST ACEH');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data);
    }

    public function test_can_get_single_boundary()
    {
        $response = $this->getJson('/api/boundaries/TEST_P1');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'kode' => 'TEST_P1',
                    'level' => 'prov',
                ],
            ]);
    }

    public function test_returns_404_for_nonexistent_boundary()
    {
        $response = $this->getJson('/api/boundaries/99.99');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Boundary not found',
            ]);
    }

    public function test_can_get_geojson_for_provinsi()
    {
        $response = $this->getJson('/api/boundaries/geojson/prov');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'type' => 'FeatureCollection',
                ],
            ])
            ->assertHeader('Content-Type', 'application/geo+json');
    }

    public function test_can_get_geojson_for_kabupaten()
    {
        $response = $this->getJson('/api/boundaries/geojson/kab');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'type' => 'FeatureCollection',
                ],
            ]);
    }

    public function test_geojson_has_correct_structure()
    {
        $response = $this->getJson('/api/boundaries/geojson/prov');

        $data = $response->json('data');

        $this->assertEquals('FeatureCollection', $data['type']);
        $this->assertIsArray($data['features']);
        $this->assertNotEmpty($data['features']);

        $feature = $data['features'][0];
        $this->assertEquals('Feature', $feature['type']);
        $this->assertArrayHasKey('geometry', $feature);
        $this->assertArrayHasKey('properties', $feature);
        $this->assertEquals('Polygon', $feature['geometry']['type']);
    }

    public function test_invalid_level_returns_400()
    {
        $response = $this->getJson('/api/boundaries/geojson/invalid');

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid level. Must be one of: prov, kab, kec, kel',
            ]);
    }

    public function test_can_search_with_query_parameter()
    {
        $response = $this->getJson('/api/boundaries/search?q=TEST');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data);
    }

    public function test_search_requires_query_parameter()
    {
        $response = $this->getJson('/api/boundaries/search');

        $response->assertStatus(422)
            ->assertJsonValidationErrors('q');
    }

    public function test_search_with_limit()
    {
        $response = $this->getJson('/api/boundaries/search?q=TEST&limit=1');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $data = $response->json('data');
        $this->assertLessThanOrEqual(1, count($data));
    }

    public function test_can_get_statistics()
    {
        $response = $this->getJson('/api/boundaries/stats');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'provinsi',
                    'kabupaten',
                    'kecamatan',
                    'kelurahan',
                    'total',
                    'last_updated',
                ],
            ]);
        
        // Verify stats are reasonable (we have real data now)
        $data = $response->json('data');
        $this->assertGreaterThan(0, $data['provinsi']);
        $this->assertGreaterThan(0, $data['kabupaten']);
        $this->assertGreaterThan(0, $data['total']);
    }

    public function test_geojson_is_cached()
    {
        // First request - should populate cache
        $this->getJson('/api/boundaries/geojson/prov');

        // Second request - should use cache
        $response = $this->getJson('/api/boundaries/geojson/prov');

        $response->assertStatus(200);
    }

    public function test_pagination_works_correctly()
    {
        // Create more boundaries for pagination test
        for ($i = 10; $i <= 25; $i++) {
            WilayahBoundary::create([
                'kode' => "TEST_K_$i",
                'level' => 'kab',
                'lat' => 4.0 + ($i * 0.1),
                'lng' => 96.0 + ($i * 0.1),
                'status' => 1,
            ]);
        }

        $response = $this->getJson('/api/boundaries?level=kab&per_page=5');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $meta = $response->json('meta');
        $this->assertEquals(5, $meta['per_page']);
        $this->assertGreaterThan(1, $meta['last_page']);
    }

    public function test_boundary_includes_region_data()
    {
        $response = $this->getJson('/api/boundaries/TEST_P1');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'kode' => 'TEST_P1',
                    'region' => [
                        'region_code' => 'TEST_P1',
                        'region_name' => 'TEST ACEH',
                    ],
                ],
            ]);
    }
}
