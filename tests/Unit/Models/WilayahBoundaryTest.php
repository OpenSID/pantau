<?php

namespace Tests\Unit\Models;

use App\Models\WilayahBoundary;
use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WilayahBoundaryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clean up any existing test data first
        WilayahBoundary::where('kode', 'like', 'TEST%')->delete();
        Region::where('region_code', 'like', 'TEST%')->delete();
        
        // Create test region with unique code for testing
        Region::create([
            'region_code' => 'TEST_PROV',
            'region_name' => 'TEST PROVINCE',
            'parent_code' => '0',
        ]);
    }

    protected function tearDown(): void
    {
        // Clean up test data after each test
        WilayahBoundary::where('kode', 'like', 'TEST%')->delete();
        Region::where('region_code', 'like', 'TEST%')->delete();
        
        parent::tearDown();
    }

    public function test_it_can_create_boundary_with_kode_as_primary_key()
    {
        $boundary = WilayahBoundary::create([
            'kode' => 'TEST_PROV',
            'level' => 'prov',
            'lat' => 4.2257,
            'lng' => 96.9119,
            'path' => [[[100.0, 0.0], [101.0, 0.0], [101.0, 1.0], [100.0, 1.0]]],
            'status' => 1,
        ]);

        $this->assertEquals('TEST_PROV', $boundary->kode);
        $this->assertEquals('prov', $boundary->level);
        $this->assertEquals(4.2257, $boundary->lat);
        $this->assertEquals(96.9119, $boundary->lng);
        $this->assertIsArray($boundary->path);
    }

    public function test_it_has_correct_casts()
    {
        $boundary = WilayahBoundary::create([
            'kode' => 'TEST_PROV',
            'level' => 'prov',
            'lat' => 4.2257,
            'lng' => 96.9119,
            'path' => [[[100.0, 0.0], [101.0, 0.0]]],  // Pass as array, not JSON string
            'status' => 1,
        ]);

        // Refresh from database to test casting
        $boundary = $boundary->fresh();

        $this->assertIsFloat($boundary->lat);
        $this->assertIsFloat($boundary->lng);
        $this->assertIsArray($boundary->path);
        $this->assertIsInt($boundary->status);
    }

    public function test_it_has_region_relationship()
    {
        $boundary = WilayahBoundary::create([
            'kode' => 'TEST_PROV',
            'level' => 'prov',
            'lat' => 4.2257,
            'lng' => 96.9119,
            'status' => 1,
        ]);

        $this->assertInstanceOf(Region::class, $boundary->region);
        $this->assertEquals('TEST PROVINCE', $boundary->region->region_name);
    }

    public function test_scope_level_filters_correctly()
    {
        WilayahBoundary::create(['kode' => 'TEST_L01', 'level' => 'prov', 'lat' => 4.0, 'lng' => 96.0, 'status' => 1]);
        WilayahBoundary::create(['kode' => 'TEST_L02', 'level' => 'kab', 'lat' => 4.1, 'lng' => 96.1, 'status' => 1]);
        WilayahBoundary::create(['kode' => 'TEST_L03', 'level' => 'kec', 'lat' => 4.2, 'lng' => 96.2, 'status' => 1]);

        $provinsi = WilayahBoundary::level('prov')->where('kode', 'like', 'TEST_%')->get();
        $kabupaten = WilayahBoundary::level('kab')->where('kode', 'like', 'TEST_%')->get();
        $kecamatan = WilayahBoundary::level('kec')->where('kode', 'like', 'TEST_%')->get();

        $this->assertCount(1, $provinsi);
        $this->assertCount(1, $kabupaten);
        $this->assertCount(1, $kecamatan);
        $this->assertEquals('TEST_L01', $provinsi->first()->kode);
    }

    public function test_scope_provinsi_filters_correctly()
    {
        WilayahBoundary::create(['kode' => 'TEST_P1', 'level' => 'prov', 'lat' => 4.0, 'lng' => 96.0, 'status' => 1]);
        WilayahBoundary::create(['kode' => 'TEST_P2', 'level' => 'prov', 'lat' => -6.0, 'lng' => 106.0, 'status' => 1]);
        WilayahBoundary::create(['kode' => 'TEST_K1', 'level' => 'kab', 'lat' => 4.1, 'lng' => 96.1, 'status' => 1]);

        $provinsi = WilayahBoundary::provinsi()->where('kode', 'like', 'TEST_%')->get();

        $this->assertCount(2, $provinsi);
    }

    public function test_scope_active_filters_correctly()
    {
        // Create test boundaries with unique codes
        $active = WilayahBoundary::create(['kode' => 'TEST_ACTIVE', 'level' => 'prov', 'lat' => 4.0, 'lng' => 96.0, 'status' => 1]);
        WilayahBoundary::create(['kode' => 'TEST_INACTIVE', 'level' => 'prov', 'lat' => -6.0, 'lng' => 106.0, 'status' => 0]);

        $activeBoundaries = WilayahBoundary::active()->where('kode', 'like', 'TEST_%')->get();

        $this->assertCount(1, $activeBoundaries);
        $this->assertEquals(1, $activeBoundaries->first()->status);
    }

    public function test_to_geo_json_feature()
    {
        $boundary = WilayahBoundary::create([
            'kode' => 'TEST_PROV',
            'level' => 'prov',
            'lat' => 4.2257,
            'lng' => 96.9119,
            'path' => [[[100.0, 0.0], [101.0, 0.0], [101.0, 1.0], [100.0, 1.0]]],
            'status' => 1,
        ]);

        $feature = $boundary->toGeoJSONFeature();

        $this->assertEquals('Feature', $feature['type']);
        $this->assertEquals('Polygon', $feature['geometry']['type']);
        $this->assertEquals('TEST_PROV', $feature['properties']['kode']);
        $this->assertEquals('prov', $feature['properties']['level']);
        $this->assertEquals('TEST PROVINCE', $feature['properties']['name']);
    }

    public function test_centroid_attribute()
    {
        $boundary = WilayahBoundary::create([
            'kode' => 'TEST_PROV',
            'level' => 'prov',
            'lat' => 4.2257,
            'lng' => 96.9119,
            'status' => 1,
        ]);

        $centroid = $boundary->centroid;

        $this->assertIsArray($centroid);
        $this->assertEquals(96.9119, $centroid[0]);
        $this->assertEquals(4.2257, $centroid[1]);
    }

    public function test_centroid_is_null_when_no_coordinates()
    {
        $boundary = WilayahBoundary::create([
            'kode' => 'TEST_PROV',
            'level' => 'prov',
            'lat' => null,
            'lng' => null,
            'status' => 1,
        ]);

        $this->assertNull($boundary->centroid);
    }
}
