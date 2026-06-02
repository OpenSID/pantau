<?php

namespace Tests\Feature\Admin\Wilayah;

use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Test untuk memastikan filter provinsi + kabupaten pada halaman
 * Data Wilayah > Kecamatan berfungsi dengan baik.
 */
class KecamatanFilterTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Halaman dapat diakses oleh user yang sudah login.
     */
    public function test_halaman_kecamatan_dapat_diakses()
    {
        $response = $this->actingAs($this->user)->get('/kecamatan');

        $response->assertStatus(200);
        $response->assertViewIs('admin.wilayah.kecamatan.index');
    }

    /**
     * Halaman memerlukan autentikasi.
     */
    public function test_halaman_kecamatan_memerlukan_autentikasi()
    {
        $response = $this->get('/kecamatan');

        $response->assertRedirect('/login');
    }

    /**
     * View menerima variabel fillters dengan key kode_provinsi dan kode_kabupaten.
     */
    public function test_view_menerima_variabel_fillters()
    {
        $response = $this->actingAs($this->user)->get('/kecamatan');

        $response->assertStatus(200);
        $response->assertViewHas('fillters', function ($fillters) {
            return array_key_exists('kode_provinsi', $fillters)
                && array_key_exists('kode_kabupaten', $fillters);
        });
    }

    /**
     * Halaman menampilkan komponen filter bertingkat (provinsi dan kabupaten).
     */
    public function test_halaman_menampilkan_komponen_filter_bertingkat()
    {
        $response = $this->actingAs($this->user)->get('/kecamatan');

        $response->assertStatus(200);

        // Tombol toggle filter
        $response->assertSee('collapse-filter', false);
        $response->assertSee('fas fa-filter', false);

        // Select2 provinsi dan kabupaten (dari form_filter component)
        $response->assertSee('id="provinsi"', false);
        $response->assertSee('id="kabupaten"', false);

        // Tombol Cari dan Reset
        $response->assertSee('id="filter"', false);
        $response->assertSee('id="reset"', false);
    }

    /**
     * Request AJAX mengembalikan format DataTables JSON.
     */
    public function test_ajax_mengembalikan_format_datatable()
    {
        $response = $this->actingAs($this->user)
            ->get('/kecamatan?length=10', [
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

    /**
     * Response DataTables memiliki kolom yang benar.
     */
    public function test_ajax_mengembalikan_kolom_yang_benar()
    {
        $response = $this->actingAs($this->user)
            ->get('/kecamatan?length=5', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);

        $data = $response->json('data');
        if (count($data) > 0) {
            $first = $data[0];
            $this->assertArrayHasKey('kode_kecamatan', $first);
            $this->assertArrayHasKey('nama_kecamatan', $first);
            $this->assertArrayHasKey('kode_kabupaten', $first);
            $this->assertArrayHasKey('nama_kabupaten', $first);
            $this->assertArrayHasKey('kode_provinsi', $first);
            $this->assertArrayHasKey('nama_provinsi', $first);
        } else {
            $this->markTestSkipped('Tidak ada data kecamatan untuk diverifikasi.');
        }
    }

    /**
     * Filter kode_provinsi mempersempit hasil (recordsFiltered < tanpa filter).
     */
    public function test_filter_kode_provinsi_mempersempit_hasil()
    {
        $provinsiList = Region::kecamatan()
            ->select('prov.region_code as kode_prov')
            ->groupBy('prov.region_code')
            ->limit(2)
            ->get();

        if ($provinsiList->count() < 2) {
            $this->markTestSkipped('Butuh minimal 2 provinsi untuk test ini.');
        }

        $kodeProvinsi = $provinsiList->first()->kode_prov;

        // Total tanpa filter
        $totalTanpa = $this->actingAs($this->user)
            ->get('/kecamatan?length=10', ['X-Requested-With' => 'XMLHttpRequest'])
            ->json('recordsFiltered');

        // Dengan filter provinsi
        $denganFilter = $this->actingAs($this->user)
            ->get("/kecamatan?length=10&kode_provinsi={$kodeProvinsi}", [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $denganFilter->assertStatus(200);
        $totalDengan = $denganFilter->json('recordsFiltered');

        $this->assertLessThanOrEqual($totalTanpa, $totalDengan);

        // Semua item dalam response harus dari provinsi yang dipilih
        foreach ($denganFilter->json('data') as $item) {
            $this->assertEquals(
                $kodeProvinsi,
                $item['kode_provinsi'],
                "Kecamatan '{$item['nama_kecamatan']}' bukan dari provinsi {$kodeProvinsi}."
            );
        }
    }

    /**
     * Filter kode_kabupaten mempersempit hasil ke kecamatan di kabupaten tersebut.
     */
    public function test_filter_kode_kabupaten_mempersempit_hasil()
    {
        // Ambil satu kabupaten yang ada
        $kabupaten = Region::kecamatan()
            ->select('kab.region_code as kode_kab', 'prov.region_code as kode_prov')
            ->groupBy('kab.region_code', 'prov.region_code')
            ->first();

        if (! $kabupaten) {
            $this->markTestSkipped('Tidak ada data kecamatan di database.');
        }

        $response = $this->actingAs($this->user)
            ->get("/kecamatan?length=10&kode_provinsi={$kabupaten->kode_prov}&kode_kabupaten={$kabupaten->kode_kab}", [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);

        // Semua item harus berasal dari kabupaten yang dipilih
        foreach ($response->json('data') as $item) {
            $this->assertEquals(
                $kabupaten->kode_kab,
                $item['kode_kabupaten'],
                "Kecamatan '{$item['nama_kecamatan']}' bukan dari kabupaten {$kabupaten->kode_kab}."
            );
        }
    }

    /**
     * Filter kabupaten saja (tanpa provinsi) tetap berfungsi.
     */
    public function test_filter_kabupaten_saja_berfungsi()
    {
        $kabupaten = Region::kecamatan()
            ->select('kab.region_code as kode_kab')
            ->groupBy('kab.region_code')
            ->first();

        if (! $kabupaten) {
            $this->markTestSkipped('Tidak ada data kecamatan di database.');
        }

        $response = $this->actingAs($this->user)
            ->get("/kecamatan?length=10&kode_kabupaten={$kabupaten->kode_kab}", [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);
    }

    /**
     * Filter yang tidak cocok menghasilkan data kosong.
     */
    public function test_filter_tidak_cocok_menghasilkan_data_kosong()
    {
        $response = $this->actingAs($this->user)
            ->get('/kecamatan?length=10&kode_provinsi=XX&kode_kabupaten=XXXXX', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $this->assertEquals(0, $response->json('recordsFiltered'));
        $this->assertEmpty($response->json('data'));
    }

    /**
     * Filter tidak mengubah data di database.
     */
    public function test_filter_tidak_mengubah_data_database()
    {
        $countBefore = Region::kecamatan()->count();

        $filters = ['kode_provinsi=32', 'kode_kabupaten=3201', 'kode_provinsi=32&kode_kabupaten=3201'];

        foreach ($filters as $param) {
            $this->actingAs($this->user)
                ->get("/kecamatan?length=10&{$param}", [
                    'X-Requested-With' => 'XMLHttpRequest',
                ])
                ->assertStatus(200);
        }

        $countAfter = Region::kecamatan()->count();
        $this->assertEquals($countBefore, $countAfter, 'Filter tidak boleh mengubah data database.');
    }
}
