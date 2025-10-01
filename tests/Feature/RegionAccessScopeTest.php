<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\User;
use App\Models\UserRegionAccess;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegionAccessScopeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_wilayah_sees_only_their_region_data()
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminWilayahRole = Role::firstOrCreate(['name' => 'Admin Wilayah']);

        // Create users
        $adminUser = User::factory()->create();
        $adminUser->assignRole($adminRole);

        $adminWilayahUser = User::factory()->create();
        $adminWilayahUser->assignRole($adminWilayahRole);

        // Create region access for Admin Wilayah
        UserRegionAccess::create([
            'user_id' => $adminWilayahUser->id,
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);

        // Create test desa data
        Desa::create([
            'nama_desa' => 'Test Desa 1',
            'kode_desa' => '1201001001',
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);

        Desa::create([
            'nama_desa' => 'Test Desa 2',
            'kode_desa' => '1202001001',
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1202',
        ]);

        // Test as Admin (should see all data)
        $this->actingAs($adminUser);
        $adminDesaCount = Desa::withoutRegionScope()->count();
        $this->assertGreaterThanOrEqual(2, $adminDesaCount);

        // Test as Admin Wilayah (should see only their kabupaten)
        $this->actingAs($adminWilayahUser);
        $adminWilayahDesaCount = Desa::count();
        $adminWilayahDesa = Desa::first();

        $this->assertEquals(1, $adminWilayahDesaCount);
        $this->assertEquals('1201', $adminWilayahDesa->kode_kabupaten);
        $this->assertEquals('Test Desa 1', $adminWilayahDesa->nama_desa);
    }

    public function test_admin_wilayah_without_region_access_sees_no_data()
    {
        $adminWilayahRole = Role::firstOrCreate(['name' => 'Admin Wilayah']);

        $adminWilayahUser = User::factory()->create();
        $adminWilayahUser->assignRole($adminWilayahRole);

        // Create test desa data
        Desa::create([
            'nama_desa' => 'Test Desa 1',
            'kode_desa' => '1201001001',
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);

        // Test as Admin Wilayah without region access (should see no data)
        $this->actingAs($adminWilayahUser);
        $desaCount = Desa::count();

        $this->assertEquals(0, $desaCount);
    }

    public function test_scope_can_be_disabled()
    {
        $adminWilayahRole = Role::firstOrCreate(['name' => 'Admin Wilayah']);

        $adminWilayahUser = User::factory()->create();
        $adminWilayahUser->assignRole($adminWilayahRole);

        UserRegionAccess::create([
            'user_id' => $adminWilayahUser->id,
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);

        // Create test desa data
        Desa::create([
            'nama_desa' => 'Test Desa 1',
            'kode_desa' => '1201001001',
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);

        Desa::create([
            'nama_desa' => 'Test Desa 2',
            'kode_desa' => '1202001001',
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1202',
        ]);

        $this->actingAs($adminWilayahUser);

        // With scope (should see only 1)
        $withScopeCount = Desa::count();
        $this->assertEquals(1, $withScopeCount);

        // Without scope (should see all)
        $withoutScopeCount = Desa::withoutRegionScope()->count();
        $this->assertGreaterThanOrEqual(2, $withoutScopeCount);
    }
}
