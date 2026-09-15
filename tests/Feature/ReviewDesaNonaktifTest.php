<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReviewDesaNonaktifTest extends TestCase
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

    public function test_review_desa_nonaktif_page_loads_with_correct_title()
    {
        $response = $this->actingAs($this->user)
            ->get('/review/non-aktif');

        $response->assertStatus(200);
        $response->assertViewIs('review.desa_nonaktif');
        $response->assertSee('Desa Tidak Aktif');
        $response->assertSee('Sejak tujuh hari terakhir');
        $response->assertDontSee('Desa Baru');
    }

    public function test_review_desa_nonaktif_ajax_returns_datatable_json()
    {
        $response = $this->actingAs($this->user)
            ->get('/review/non-aktif', [
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

    public function test_inactive_desa_is_included_and_active_desa_is_excluded()
    {
        $uniqueSuffix = uniqid();

        // Inactive desa (accessed 10 days ago, > 7 days)
        $desaInactive = Desa::factory()->create([
            'nama_desa' => 'DesaNonAktif_' . $uniqueSuffix,
            'updated_at' => now()->subDays(10),
            'tgl_akses_lokal' => now()->subDays(10),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // Active desa (accessed 3 days ago, <= 7 days)
        $desaActive = Desa::factory()->create([
            'nama_desa' => 'DesaAktif_' . $uniqueSuffix,
            'updated_at' => now()->subDays(3),
            'tgl_akses_lokal' => now()->subDays(3),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/review/non-aktif?length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $data = $response->json('data');
        $names = collect($data)->pluck('nama_desa')->toArray();

        $this->assertContains($desaInactive->nama_desa, $names);
        $this->assertNotContains($desaActive->nama_desa, $names);
    }

    public function test_mutual_exclusivity_between_desa_aktif_and_desa_nonaktif()
    {
        $uniqueSuffix = uniqid();

        // Desa inactive: 12 days ago (> 7 days)
        $desaInactive = Desa::factory()->create([
            'nama_desa' => 'DesaMutExInactive_' . $uniqueSuffix,
            'updated_at' => now()->subDays(12),
            'tgl_akses_lokal' => now()->subDays(12),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // Desa active: 2 days ago (<= 7 days)
        $desaActive = Desa::factory()->create([
            'nama_desa' => 'DesaMutExActive_' . $uniqueSuffix,
            'updated_at' => now()->subDays(2),
            'tgl_akses_lokal' => now()->subDays(2),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // Check /review/non-aktif (Desa Tidak Aktif)
        $responseNonAktif = $this->actingAs($this->user)
            ->get('/review/non-aktif?length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $responseNonAktif->assertStatus(200);
        $nonAktifNames = collect($responseNonAktif->json('data'))->pluck('nama_desa')->toArray();

        $this->assertContains($desaInactive->nama_desa, $nonAktifNames);
        $this->assertNotContains($desaActive->nama_desa, $nonAktifNames);

        // Check /laporan/desa-aktif (Desa Aktif)
        $responseAktif = $this->actingAs($this->user)
            ->get('/laporan/desa-aktif?length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $responseAktif->assertStatus(200);
        $aktifNames = collect($responseAktif->json('data'))->pluck('nama_desa')->toArray();

        $this->assertContains($desaActive->nama_desa, $aktifNames);
        $this->assertNotContains($desaInactive->nama_desa, $aktifNames);
    }

    public function test_tgl_akses_format_and_field_in_review_desa_nonaktif()
    {
        $uniqueSuffix = uniqid();
        $accessDate = now()->subDays(10);

        $desa = Desa::factory()->create([
            'nama_desa' => 'DesaTglAkses_' . $uniqueSuffix,
            'updated_at' => $accessDate,
            'tgl_akses_lokal' => $accessDate,
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/review/non-aktif?length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $data = $response->json('data');
        $desaData = collect($data)->firstWhere('nama_desa', $desa->nama_desa);

        $this->assertNotNull($desaData);
        $this->assertArrayHasKey('tgl_akses', $desaData);
        $this->assertEquals($accessDate->format('Y-m-d'), $desaData['tgl_akses']);
    }
}
