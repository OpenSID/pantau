<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReviewDesaBaruTest extends TestCase
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

    public function test_review_desa_baru_page_loads_with_filter_component()
    {
        $response = $this->actingAs($this->user)
            ->get('/review/desa-baru');

        $response->assertStatus(200);
        $response->assertViewIs('review.desa_baru');
        $response->assertSee('Desa Baru');
        $response->assertSee('collapse-filter');
        $response->assertSee('id="akses"', false);
        $response->assertSee('1 Bulan Terakhir');
        $response->assertSee('6 Bulan Terakhir');
    }

    public function test_review_desa_baru_ajax_returns_datatable_json()
    {
        $response = $this->actingAs($this->user)
            ->get('/review/desa-baru', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
    }

    public function test_review_desa_baru_filter_akses_timeframe()
    {
        $uniqueSuffix = uniqid();

        // Desa baru 3 days ago (within 7 days, 1 month, 6 months)
        $desa3Hari = Desa::factory()->create([
            'nama_desa' => 'DesaBaru3H_' . $uniqueSuffix,
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(3),
            'tgl_akses_lokal' => now()->subDays(3),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // Desa baru 20 days ago (outside 7 days, within 1 month, 6 months)
        $desa20Hari = Desa::factory()->create([
            'nama_desa' => 'DesaBaru20H_' . $uniqueSuffix,
            'created_at' => now()->subDays(20),
            'updated_at' => now()->subDays(20),
            'tgl_akses_lokal' => now()->subDays(20),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // Desa baru 100 days ago (~3.3 months, outside 1 month, within 6 months)
        $desa100Hari = Desa::factory()->create([
            'nama_desa' => 'DesaBaru100H_' . $uniqueSuffix,
            'created_at' => now()->subDays(100),
            'updated_at' => now()->subDays(100),
            'tgl_akses_lokal' => now()->subDays(100),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // 1. Default (tanpa filter akses) -> 7 hari terakhir
        $responseDefault = $this->actingAs($this->user)
            ->get('/review/desa-baru?length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $responseDefault->assertStatus(200);
        $namesDefault = collect($responseDefault->json('data'))->pluck('nama_desa')->toArray();
        $this->assertContains($desa3Hari->nama_desa, $namesDefault);
        $this->assertNotContains($desa20Hari->nama_desa, $namesDefault);
        $this->assertNotContains($desa100Hari->nama_desa, $namesDefault);

        // 2. Filter: 7 Hari Terakhir (akses=4)
        $response7Hari = $this->actingAs($this->user)
            ->get('/review/desa-baru?akses=4&length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $response7Hari->assertStatus(200);
        $names7Hari = collect($response7Hari->json('data'))->pluck('nama_desa')->toArray();
        $this->assertContains($desa3Hari->nama_desa, $names7Hari);
        $this->assertNotContains($desa20Hari->nama_desa, $names7Hari);
        $this->assertNotContains($desa100Hari->nama_desa, $names7Hari);

        // 3. Filter: 1 Bulan Terakhir (akses=6)
        $response1Bulan = $this->actingAs($this->user)
            ->get('/review/desa-baru?akses=6&length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $response1Bulan->assertStatus(200);
        $names1Bulan = collect($response1Bulan->json('data'))->pluck('nama_desa')->toArray();
        $this->assertContains($desa3Hari->nama_desa, $names1Bulan);
        $this->assertContains($desa20Hari->nama_desa, $names1Bulan);
        $this->assertNotContains($desa100Hari->nama_desa, $names1Bulan);

        // 4. Filter: 6 Bulan Terakhir (akses=8)
        $response6Bulan = $this->actingAs($this->user)
            ->get('/review/desa-baru?akses=8&length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $response6Bulan->assertStatus(200);
        $names6Bulan = collect($response6Bulan->json('data'))->pluck('nama_desa')->toArray();
        $this->assertContains($desa3Hari->nama_desa, $names6Bulan);
        $this->assertContains($desa20Hari->nama_desa, $names6Bulan);
        $this->assertContains($desa100Hari->nama_desa, $names6Bulan);
    }
}
