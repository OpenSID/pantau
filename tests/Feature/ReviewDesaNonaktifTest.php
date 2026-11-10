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
        $response->assertSee('Tidak diakses dalam 30 hari terakhir');
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

        // Inactive desa (accessed 35 days ago, outside 30-day window)
        $desaInactive = Desa::factory()->create([
            'nama_desa' => 'DesaNonAktif_' . $uniqueSuffix,
            'updated_at' => now()->subDays(35),
            'tgl_akses_lokal' => now()->subDays(35),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // Active desa (accessed 3 days ago, inside 30-day window)
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

        // Desa inactive: 35 days ago (outside 30-day window)
        $desaInactive = Desa::factory()->create([
            'nama_desa' => 'DesaMutExInactive_' . $uniqueSuffix,
            'updated_at' => now()->subDays(35),
            'tgl_akses_lokal' => now()->subDays(35),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // Desa active: 2 days ago (inside 30-day window)
        $desaActive = Desa::factory()->create([
            'nama_desa' => 'DesaMutExActive_' . $uniqueSuffix,
            'updated_at' => now()->subDays(2),
            'tgl_akses_lokal' => now()->subDays(2),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // Desa diakses 15 hari lalu: dulu (batas 7 hari) masuk Nonaktif, kini wajib masuk Aktif saja (#745)
        $desa15Hari = Desa::factory()->create([
            'nama_desa' => 'DesaMutEx15Hari_' . $uniqueSuffix,
            'updated_at' => now()->subDays(15),
            'tgl_akses_lokal' => now()->subDays(15),
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
        $this->assertNotContains($desa15Hari->nama_desa, $nonAktifNames);

        // Check /laporan/desa-aktif (Desa Aktif)
        $responseAktif = $this->actingAs($this->user)
            ->get('/laporan/desa-aktif?length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $responseAktif->assertStatus(200);
        $aktifNames = collect($responseAktif->json('data'))->pluck('nama_desa')->toArray();

        $this->assertContains($desaActive->nama_desa, $aktifNames);
        $this->assertContains($desa15Hari->nama_desa, $aktifNames);
        $this->assertNotContains($desaInactive->nama_desa, $aktifNames);

        // Tidak ada desa yang tampil di kedua daftar sekaligus
        $this->assertEmpty(array_intersect(
            [$desaActive->nama_desa, $desa15Hari->nama_desa, $desaInactive->nama_desa],
            $aktifNames,
            $nonAktifNames
        ));
    }

    public function test_tgl_akses_format_and_field_in_review_desa_nonaktif()
    {
        $uniqueSuffix = uniqid();
        $accessDate = now()->subDays(35);

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

    public function test_review_desa_nonaktif_page_has_filter_component()
    {
        $response = $this->actingAs($this->user)
            ->get('/review/non-aktif');

        $response->assertStatus(200);
        $response->assertSee('collapse-filter');
        $response->assertSee('id="akses"', false);
        $response->assertSee('1 Bulan Terakhir');
        $response->assertSee('6 Bulan Terakhir');
    }

    public function test_review_desa_nonaktif_filter_akses()
    {
        $uniqueSuffix = uniqid();

        // Inactive desa 45 days ago (outside 30-day window, within 2 months)
        $desa45Hari = Desa::factory()->create([
            'nama_desa' => 'Desa45Hari_' . $uniqueSuffix,
            'updated_at' => now()->subDays(45),
            'tgl_akses_lokal' => now()->subDays(45),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // Inactive desa 60 days ago (outside 1 month, within 6 months)
        $desa60Hari = Desa::factory()->create([
            'nama_desa' => 'Desa60Hari_' . $uniqueSuffix,
            'updated_at' => now()->subDays(60),
            'tgl_akses_lokal' => now()->subDays(60),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // Inactive desa 250 days ago (outside 6 months, > 6 months)
        $desa250Hari = Desa::factory()->create([
            'nama_desa' => 'Desa250Hari_' . $uniqueSuffix,
            'updated_at' => now()->subDays(250),
            'tgl_akses_lokal' => now()->subDays(250),
            'tgl_akses_hosting' => null,
            'jenis' => 1,
        ]);

        // 1. Filter: 2 Bulan Terakhir (akses=2)
        $responseBulan2 = $this->actingAs($this->user)
            ->get('/review/non-aktif?akses=2&length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $responseBulan2->assertStatus(200);
        $namesBulan2 = collect($responseBulan2->json('data'))->pluck('nama_desa')->toArray();
        $this->assertContains($desa45Hari->nama_desa, $namesBulan2);
        $this->assertNotContains($desa250Hari->nama_desa, $namesBulan2);

        // 1b. Filter: 1 Bulan Terakhir (akses=6) — irisan dengan "tidak aktif 30 hari" selalu kosong
        $responseBulan1 = $this->actingAs($this->user)
            ->get('/review/non-aktif?akses=6&length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $responseBulan1->assertStatus(200);
        $namesBulan1 = collect($responseBulan1->json('data'))->pluck('nama_desa')->toArray();
        $this->assertNotContains($desa45Hari->nama_desa, $namesBulan1);
        $this->assertNotContains($desa60Hari->nama_desa, $namesBulan1);
        $this->assertNotContains($desa250Hari->nama_desa, $namesBulan1);

        // 2. Filter: 6 Bulan Terakhir (akses=8)
        $responseBulan6 = $this->actingAs($this->user)
            ->get('/review/non-aktif?akses=8&length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $responseBulan6->assertStatus(200);
        $namesBulan6 = collect($responseBulan6->json('data'))->pluck('nama_desa')->toArray();
        $this->assertContains($desa45Hari->nama_desa, $namesBulan6);
        $this->assertContains($desa60Hari->nama_desa, $namesBulan6);
        $this->assertNotContains($desa250Hari->nama_desa, $namesBulan6);

        // 3. Filter: Sebelum 6 Bulan yang Lalu (akses=9)
        $responseSblm6 = $this->actingAs($this->user)
            ->get('/review/non-aktif?akses=9&length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $responseSblm6->assertStatus(200);
        $namesSblm6 = collect($responseSblm6->json('data'))->pluck('nama_desa')->toArray();
        $this->assertNotContains($desa45Hari->nama_desa, $namesSblm6);
        $this->assertNotContains($desa60Hari->nama_desa, $namesSblm6);
        $this->assertContains($desa250Hari->nama_desa, $namesSblm6);
    }
}
