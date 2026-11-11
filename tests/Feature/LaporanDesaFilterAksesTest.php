<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LaporanDesaFilterAksesTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_laporan_desa_page_loads_with_standardized_akses_options()
    {
        $response = $this->actingAs($this->user)
            ->get('/laporan/desa');

        $response->assertStatus(200);
        $response->assertSee('7 Hari Terakhir');
        $response->assertSee('1 Bulan Terakhir');
        $response->assertSee('3 Bulan Terakhir');
        $response->assertSee('6 Bulan Terakhir');
        $response->assertSee('Sebelum 6 Bulan yang Lalu');
        $response->assertSee('Desa aktif hanya offline');
    }

    public function test_laporan_desa_filter_akses_query()
    {
        $uniqueSuffix = uniqid();

        // Desa accessed 10 days ago (within 1 month, within 6 months)
        $desa10Hari = Desa::factory()->create([
            'nama_desa' => 'DesaLaporan10H_' . $uniqueSuffix,
            'updated_at' => now()->subDays(10),
            'tgl_akses_lokal' => now()->subDays(10),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // Desa accessed 45 days ago (outside 1 month, within 6 months)
        $desa45Hari = Desa::factory()->create([
            'nama_desa' => 'DesaLaporan45H_' . $uniqueSuffix,
            'updated_at' => now()->subDays(45),
            'tgl_akses_lokal' => now()->subDays(45),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // Desa accessed 220 days ago (outside 6 months, > 6 months)
        $desa220Hari = Desa::factory()->create([
            'nama_desa' => 'DesaLaporan220H_' . $uniqueSuffix,
            'updated_at' => now()->subDays(220),
            'tgl_akses_lokal' => now()->subDays(220),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // 1. Filter: 1 Bulan Terakhir (akses=6)
        $response1Bulan = $this->actingAs($this->user)
            ->get('/laporan/desa?akses=6&length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $response1Bulan->assertStatus(200);
        $names1Bulan = collect($response1Bulan->json('data'))->pluck('nama_desa')->toArray();
        $this->assertContains($desa10Hari->nama_desa, $names1Bulan);
        $this->assertNotContains($desa45Hari->nama_desa, $names1Bulan);
        $this->assertNotContains($desa220Hari->nama_desa, $names1Bulan);

        // 2. Filter: 6 Bulan Terakhir (akses=8)
        $response6Bulan = $this->actingAs($this->user)
            ->get('/laporan/desa?akses=8&length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $response6Bulan->assertStatus(200);
        $names6Bulan = collect($response6Bulan->json('data'))->pluck('nama_desa')->toArray();
        $this->assertContains($desa10Hari->nama_desa, $names6Bulan);
        $this->assertContains($desa45Hari->nama_desa, $names6Bulan);
        $this->assertNotContains($desa220Hari->nama_desa, $names6Bulan);

        // 3. Filter: Sebelum 6 Bulan yang Lalu (akses=9)
        $responseSblm6 = $this->actingAs($this->user)
            ->get('/laporan/desa?akses=9&length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $responseSblm6->assertStatus(200);
        $namesSblm6 = collect($responseSblm6->json('data'))->pluck('nama_desa')->toArray();
        $this->assertNotContains($desa10Hari->nama_desa, $namesSblm6);
        $this->assertNotContains($desa45Hari->nama_desa, $namesSblm6);
        $this->assertContains($desa220Hari->nama_desa, $namesSblm6);
    }
}
