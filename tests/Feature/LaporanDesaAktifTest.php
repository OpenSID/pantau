<?php

namespace Tests\Feature;

use App\Models\Akses;
use App\Models\Desa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LaporanDesaAktifTest extends TestCase
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

    public function test_laporan_desa_aktif_page_loads()
    {
        $response = $this->actingAs($this->user)
            ->get('/laporan/desa-aktif');

        $response->assertStatus(200);
        $response->assertViewIs('laporan.desa_aktif');
    }

    public function test_laporan_desa_aktif_ajax_returns_datatable_json()
    {
        $response = $this->actingAs($this->user)
            ->get('/laporan/desa-aktif?length=999', [
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

    public function test_active_desa_is_included_in_response()
    {
        // Create active desa (within 30 days)
        $desa = Desa::factory()->create([
            'nama_desa' => 'DesaTestAktif_' . uniqid(),
            'updated_at' => now()->subDays(5),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/laporan/desa-aktif?length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $names = collect($data)->pluck('nama_desa')->toArray();
        $this->assertContains($desa->nama_desa, $names);
    }

    public function test_akses_count_is_present_in_response()
    {
        $desa = Desa::factory()->create([
            'nama_desa' => 'DesaAksesCount_' . uniqid(),
            'updated_at' => now(),
        ]);

        // Create recent akses records (within 30 days)
        Akses::factory()->count(3)->create([
            'desa_id' => $desa->id,
            'created_at' => now()->subDays(5),
        ]);

        // Create old akses records (older than 30 days, should NOT be counted)
        Akses::factory()->count(2)->create([
            'desa_id' => $desa->id,
            'created_at' => now()->subDays(45),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/laporan/desa-aktif?length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $desaData = collect($data)->firstWhere('nama_desa', $desa->nama_desa);
        $this->assertNotNull($desaData, 'Test desa should be in response');
        $this->assertEquals(3, $desaData['akses_count'], 'Only recent akses should be counted');
    }

    public function test_filter_by_provinsi_works()
    {
        $uniqueSuffix = uniqid();

        $desaJawa = Desa::factory()->create([
            'nama_desa' => 'DesaJawa_' . $uniqueSuffix,
            'kode_provinsi' => '99',
            'updated_at' => now(),
        ]);

        $desaSumatra = Desa::factory()->create([
            'nama_desa' => 'DesaSumatra_' . $uniqueSuffix,
            'kode_provinsi' => '98',
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/laporan/desa-aktif?kode_provinsi=99&length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $names = collect($data)->pluck('nama_desa')->toArray();
        $this->assertContains($desaJawa->nama_desa, $names);
        $this->assertNotContains($desaSumatra->nama_desa, $names);
    }
}
