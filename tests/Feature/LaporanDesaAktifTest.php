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
        // Create active desa (within 7 days)
        $desa = Desa::factory()->create([
            'nama_desa' => 'DesaTestAktif_' . uniqid(),
            'updated_at' => now()->subDays(3),
            'tgl_akses_lokal' => now()->subDays(3),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/laporan/desa-aktif?length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $names = collect($data)->pluck('nama_desa')->toArray();
        $this->assertContains($desa->nama_desa, $names);

        $desaData = collect($data)->firstWhere('nama_desa', $desa->nama_desa);
        $this->assertNotNull($desaData);
        $this->assertArrayHasKey('tgl_akses', $desaData);
        $this->assertEquals(now()->subDays(3)->format('Y-m-d'), $desaData['tgl_akses']);
    }

    public function test_inactive_desa_is_excluded_from_response()
    {
        // Create inactive desa (di luar 30 hari kalender)
        $desa = Desa::factory()->create([
            'nama_desa' => 'DesaTestInaktif_' . uniqid(),
            'updated_at' => now()->subDays(35),
            'tgl_akses_lokal' => now()->subDays(35),
        ]);

        $response = $this->actingAs($this->user)
            ->get('/laporan/desa-aktif?length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $names = collect($data)->pluck('nama_desa')->toArray();
        $this->assertNotContains($desa->nama_desa, $names);
    }

    public function test_akses_count_is_present_in_response()
    {
        $desa = Desa::factory()->create([
            'nama_desa' => 'DesaAksesCount_' . uniqid(),
            'updated_at' => now(),
            'tgl_akses_lokal' => now()->subDays(3),
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
            'tgl_akses_lokal' => now()->subDays(3),
        ]);

        $desaSumatra = Desa::factory()->create([
            'nama_desa' => 'DesaSumatra_' . $uniqueSuffix,
            'kode_provinsi' => '98',
            'updated_at' => now(),
            'tgl_akses_lokal' => now()->subDays(3),
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

    /**
     * Ambil daftar nama desa dari endpoint DataTables Desa Aktif.
     */
    private function namaDesaAktif(string $query = ''): array
    {
        $response = $this->actingAs($this->user)
            ->get('/laporan/desa-aktif?length=-1' . ($query ? '&' . $query : ''), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);

        return collect($response->json('data'))->pluck('nama_desa')->toArray();
    }

    /**
     * Regresi #745: desa yang diakses 8-29 hari lalu dulu tidak tampil (batas 7 hari).
     */
    public function test_desa_diakses_10_hari_lalu_termasuk_aktif()
    {
        $desa = Desa::factory()->create([
            'nama_desa' => 'Desa10Hari_' . uniqid(),
            'tgl_akses_lokal' => now()->subDays(10),
            'tgl_akses_hosting' => null,
        ]);

        $this->assertContains($desa->nama_desa, $this->namaDesaAktif());
    }

    /**
     * Batas hari: hari ke-29 masuk, hari ke-30 keluar.
     * Jam 12:00 dipakai agar tahan selisih zona waktu PHP vs MySQL (DATE(now()) dihitung MySQL).
     */
    public function test_batas_hari_ke_29_masuk_dan_hari_ke_30_keluar()
    {
        $suffix = uniqid();

        $desaHari29 = Desa::factory()->create([
            'nama_desa' => 'DesaHari29_' . $suffix,
            'tgl_akses_lokal' => null,
            'tgl_akses_hosting' => now()->subDays(29)->setTime(12, 0),
        ]);

        $desaHari30 = Desa::factory()->create([
            'nama_desa' => 'DesaHari30_' . $suffix,
            'tgl_akses_lokal' => null,
            'tgl_akses_hosting' => now()->subDays(30)->setTime(12, 0),
        ]);

        $names = $this->namaDesaAktif();

        $this->assertContains($desaHari29->nama_desa, $names);
        $this->assertNotContains($desaHari30->nama_desa, $names);
    }

    /**
     * Jumlah di menu Desa Aktif sama dengan kartu "Desa Aktif" dashboard admin untuk filter yang sama.
     */
    public function test_jumlah_sama_dengan_kartu_dashboard_admin()
    {
        Desa::factory()->create(['kode_provinsi' => '99', 'tgl_akses_lokal' => now()->subDays(2)]);
        Desa::factory()->create(['kode_provinsi' => '99', 'tgl_akses_lokal' => now()->subDays(20)]);
        Desa::factory()->create(['kode_provinsi' => '99', 'tgl_akses_lokal' => now()->subDays(40)]);

        foreach (['', 'kode_provinsi=99'] as $query) {
            parse_str($query, $params);
            $this->app['request']->merge($params);

            $this->actingAs($this->user);
            $kartu = (int) Desa::jumlahDesa()->first()->aktif;

            $response = $this->get('/laporan/desa-aktif?length=-1' . ($query ? '&' . $query : ''), [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

            $response->assertStatus(200);
            $this->assertSame($kartu, (int) $response->json('recordsFiltered'), "Filter: '{$query}'");

            $this->app['request']->replace([]);
        }
    }

    public function test_filter_aktif_mingguan_hanya_7_hari()
    {
        $suffix = uniqid();

        $desa3Hari = Desa::factory()->create([
            'nama_desa' => 'DesaMingguan3_' . $suffix,
            'tgl_akses_lokal' => now()->subDays(3),
        ]);
        $desa10Hari = Desa::factory()->create([
            'nama_desa' => 'DesaMingguan10_' . $suffix,
            'tgl_akses_lokal' => now()->subDays(10),
        ]);

        $names = $this->namaDesaAktif('akses=4');

        $this->assertContains($desa3Hari->nama_desa, $names);
        $this->assertNotContains($desa10Hari->nama_desa, $names);
    }

    public function test_label_filter_aktif_mingguan_dan_bulanan()
    {
        $response = $this->actingAs($this->user)->get('/laporan/desa-aktif');

        $response->assertStatus(200);
        $response->assertSee('30 hari terakhir');
        $response->assertSee('Aktif Mingguan (7 hari)');
        $response->assertSee('Aktif Bulanan (30 hari)');
        $response->assertDontSee('7 Hari Terakhir');
    }

    public function test_akses_count_mengikuti_30_hari_kalender()
    {
        $desa = Desa::factory()->create([
            'nama_desa' => 'DesaAksesBatas_' . uniqid(),
            'tgl_akses_lokal' => now()->subDays(1),
        ]);

        Akses::factory()->create(['desa_id' => $desa->id, 'created_at' => now()->subDays(29)->setTime(0, 30)]);
        Akses::factory()->create(['desa_id' => $desa->id, 'created_at' => now()->subDays(30)->setTime(23, 30)]);

        $response = $this->actingAs($this->user)
            ->get('/laporan/desa-aktif?length=-1', ['X-Requested-With' => 'XMLHttpRequest']);

        $desaData = collect($response->json('data'))->firstWhere('nama_desa', $desa->nama_desa);
        $this->assertNotNull($desaData);
        $this->assertEquals(1, $desaData['akses_count']);
    }
}
