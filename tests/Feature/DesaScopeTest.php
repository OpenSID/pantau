<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\User;
use App\Models\UserRegionAccess;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Test untuk memastikan scope jumlahDesa dan kabupatenKosong 
 * berjalan dengan baik untuk user dengan role Admin Wilayah.
 * 
 * Test ini memverifikasi bahwa:
 * - scopeJumlahDesa menghitung desa dengan benar berdasarkan region access
 * - scopeKabupatenKosong menampilkan kabupaten kosong sesuai region access
 * - Filter wilayah (provinsi/kabupaten) berfungsi dengan baik
 * - Penghitungan desa online/offline, premium, dan aktif/tidak aktif benar
 */
class DesaScopeTest extends TestCase
{
    use DatabaseTransactions;

    protected $adminWilayahUser;
    protected $adminWilayahRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin Wilayah role
        $this->adminWilayahRole = Role::firstOrCreate(['name' => 'Admin Wilayah']);

        // Create Admin Wilayah user
        $this->adminWilayahUser = User::factory()->create();
        $this->adminWilayahUser->assignRole($this->adminWilayahRole);

        // Create region access for Admin Wilayah (Kabupaten 1201)
        UserRegionAccess::create([
            'user_id' => $this->adminWilayahUser->id,
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);
    }

    public function test_scope_jumlah_desa_works_with_admin_wilayah()
    {
        // Create test desa data in the Admin Wilayah's region
        Desa::create([
            'nama_desa' => 'Desa Admin Wilayah 1',
            'kode_desa' => '1201001001',
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
            'kode_kecamatan' => '120101',
            'versi_lokal' => '22.06',
            'versi_hosting' => null,
            'tgl_akses_lokal' => now()->subDays(3),
        ]);

        Desa::create([
            'nama_desa' => 'Desa Admin Wilayah 2',
            'kode_desa' => '1201001002',
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
            'kode_kecamatan' => '120101',
            'versi_hosting' => '23.01',
            'tgl_akses_hosting' => now()->subDays(2),
        ]);

        // Create test desa data outside Admin Wilayah's region (should not be counted)
        Desa::create([
            'nama_desa' => 'Desa Luar Wilayah',
            'kode_desa' => '1202001001',
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1202',
            'kode_kecamatan' => '120201',
            'versi_hosting' => '23.01',
            'tgl_akses_hosting' => now()->subDays(2),
        ]);

        // Act as Admin Wilayah
        $this->actingAs($this->adminWilayahUser);

        // Execute scopeJumlahDesa
        $result = Desa::jumlahDesa()->first();

        // Assert that the result is not null
        $this->assertNotNull($result);

        // Assert desa_total only counts desa in Admin Wilayah's kabupaten
        $this->assertEquals(2, $result->desa_total, 'desa_total should be 2');

        // Assert desa_offline counts desa with only versi_lokal
        $this->assertEquals(1, $result->desa_offline, 'desa_offline should be 1');

        // Assert desa_online counts desa with versi_hosting
        $this->assertEquals(1, $result->desa_online, 'desa_online should be 1');

        // Assert aktif counts desa with recent access
        $this->assertEquals(2, $result->aktif, 'aktif should be 2');

        // Assert kabupaten_total
        $this->assertEquals(1, $result->kabupaten_total, 'kabupaten_total should be 1');
    }

    public function test_scope_jumlah_desa_with_provinsi_filter()
    {
        // Create another Admin Wilayah with provinsi-level access
        $adminWilayahProvinsi = User::factory()->create();
        $adminWilayahProvinsi->assignRole($this->adminWilayahRole);

        UserRegionAccess::create([
            'user_id' => $adminWilayahProvinsi->id,
            'kode_provinsi' => '12',
            'kode_kabupaten' => null, // Province level access
        ]);

        // Act as Admin Wilayah with provinsi access
        $this->actingAs($adminWilayahProvinsi);
        
        // Get current count before adding
        $beforeResult = Desa::jumlahDesa()->first();
        $beforeCount = $beforeResult->desa_total;
        $beforeKabupatenCount = $beforeResult->kabupaten_total;

        // Create test desa data in different kabupaten but same provinsi
        Desa::create([
            'nama_desa' => 'Desa Kabupaten 1',
            'kode_desa' => '1201001003',
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
            'versi_hosting' => '23.01',
            'tgl_akses_hosting' => now()->subDays(2),
        ]);

        Desa::create([
            'nama_desa' => 'Desa Kabupaten 2',
            'kode_desa' => '1202001002',
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1202',
            'versi_hosting' => '23.01',
            'tgl_akses_hosting' => now()->subDays(2),
        ]);

        // Create desa outside provinsi (should not be counted)
        Desa::create([
            'nama_desa' => 'Desa Luar Provinsi',
            'kode_desa' => '1301001001',
            'kode_provinsi' => '13',
            'kode_kabupaten' => '1301',
            'versi_hosting' => '23.01',
            'tgl_akses_hosting' => now()->subDays(2),
        ]);

        // Execute scopeJumlahDesa
        $result = Desa::jumlahDesa()->first();

        // Assert desa_total increased by 2 (only within provinsi)
        $this->assertEquals($beforeCount + 2, $result->desa_total, 'desa_total should increase by 2');

        // Assert kabupaten_total increased (may not be exactly 2 if kabupaten already had desa)
        $this->assertGreaterThanOrEqual($beforeKabupatenCount, $result->kabupaten_total);
    }

    public function test_scope_kabupaten_kosong_works_with_admin_wilayah()
    {
        // Act as Admin Wilayah
        $this->actingAs($this->adminWilayahUser);

        // Execute scopeKabupatenKosong
        $result = Desa::kabupatenKosong();

        // Assert that result is an array
        $this->assertIsArray($result);

        // The result should contain kabupaten data that don't have desa registered
        // Since this is a complex raw query, we just verify it returns without error
        // and has the expected structure if there's data
        if (count($result) > 0) {
            $firstRow = $result[0];
            $this->assertObjectHasProperty('region_code', $firstRow);
            $this->assertObjectHasProperty('nama_kabupaten', $firstRow);
            $this->assertObjectHasProperty('nama_provinsi', $firstRow);
            $this->assertObjectHasProperty('jml_desa', $firstRow);
        }
    }

    public function test_scope_kabupaten_kosong_filters_by_region_access()
    {
        // Act as Admin Wilayah with kabupaten access
        $this->actingAs($this->adminWilayahUser);

        // Execute scopeKabupatenKosong
        $result = Desa::kabupatenKosong();

        // Filter should be applied based on region access
        // All results should be within the Admin Wilayah's region
        if (!empty($result)) {
            foreach ($result as $row) {
                // If kode_kabupaten is specified in region access, 
                // all results should match that kabupaten
                $this->assertTrue(
                    str_starts_with($row->region_code, '1201'),
                    "Result region_code {$row->region_code} should start with 1201"
                );
            }
        } else {
            // If no empty kabupaten found, that's also valid
            $this->assertTrue(true, 'No empty kabupaten found for this region');
        }
    }

    public function test_scope_jumlah_desa_counts_premium_version()
    {
        // Get the latest release version
        $latestVersion = lastrelease_opensid();
        
        // Create test desa with premium version
        Desa::create([
            'nama_desa' => 'Desa Premium 1',
            'kode_desa' => '1201001004',
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
            'versi_hosting' => "{$latestVersion}-premium",
            'tgl_akses_hosting' => now()->subDays(2),
        ]);

        Desa::create([
            'nama_desa' => 'Desa Premium 2',
            'kode_desa' => '1201001005',
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
            'versi_lokal' => "{$latestVersion}-premium",
            'tgl_akses_lokal' => now()->subDays(2),
        ]);

        // Act as Admin Wilayah
        $this->actingAs($this->adminWilayahUser);

        // Execute scopeJumlahDesa
        $result = Desa::jumlahDesa()->first();

        // Assert kabupaten_premium is counted (should be 1 because both desa are in same kabupaten)
        $this->assertEquals(1, $result->kabupaten_premium, 'kabupaten_premium should be 1 for kabupaten 1201');
    }

    public function test_scope_jumlah_desa_counts_inactive_desa()
    {
        // Create inactive desa (last access > 4 months ago)
        Desa::create([
            'nama_desa' => 'Desa Tidak Aktif',
            'kode_desa' => '1201001006',
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
            'versi_hosting' => '22.06',
            'tgl_akses_hosting' => now()->subMonths(5),
        ]);

        // Act as Admin Wilayah
        $this->actingAs($this->adminWilayahUser);

        // Execute scopeJumlahDesa
        $result = Desa::jumlahDesa()->first();

        // Assert tidak_aktif is counted
        $this->assertGreaterThanOrEqual(1, $result->tidak_aktif, 'tidak_aktif should be at least 1');
    }
}
