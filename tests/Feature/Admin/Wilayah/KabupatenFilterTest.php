<?php

namespace Tests\Feature\Admin\Wilayah;

use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Test untuk memastikan filter provinsi pada halaman Data Wilayah > Kabupaten
 * berfungsi dengan baik.
 *
 * Mencakup:
 * - Halaman dapat diakses dan merender view yang benar
 * - Halaman memerlukan autentikasi
 * - Komponen filter (Select2 + tombol) ada di halaman
 * - DataTables AJAX mengembalikan struktur JSON yang benar
 * - Filter kode_provinsi mempersempit hasil yang dikembalikan
 * - Filter tidak mengubah data di database
 */
class KabupatenFilterTest extends TestCase
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
    public function test_halaman_kabupaten_dapat_diakses()
    {
        $response = $this->actingAs($this->user)->get('/kabupaten');

        $response->assertStatus(200);
        $response->assertViewIs('admin.wilayah.kabupaten.index');
    }

    /**
     * Halaman memerlukan autentikasi.
     */
    public function test_halaman_kabupaten_memerlukan_autentikasi()
    {
        $response = $this->get('/kabupaten');

        $response->assertRedirect('/login');
    }

    /**
     * View menerima variabel fillters dengan key kode_provinsi.
     */
    public function test_view_menerima_variabel_fillters()
    {
        $response = $this->actingAs($this->user)->get('/kabupaten');

        $response->assertStatus(200);
        $response->assertViewHas('fillters', function ($fillters) {
            return array_key_exists('kode_provinsi', $fillters);
        });
    }

    /**
     * Halaman menampilkan komponen filter wilayah (tombol & select2 provinsi).
     */
    public function test_halaman_menampilkan_komponen_filter()
    {
        $response = $this->actingAs($this->user)->get('/kabupaten');

        $response->assertStatus(200);

        // Tombol toggle filter
        $response->assertSee('collapse-filter', false);
        $response->assertSee('fas fa-filter', false);

        // Select2 provinsi (dari form_filter component)
        $response->assertSee('id="provinsi"', false);

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
            ->get('/kabupaten?length=10', [
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
            ->get('/kabupaten?length=5', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);

        $data = $response->json('data');
        if (count($data) > 0) {
            $first = $data[0];
            $this->assertArrayHasKey('kode_kabupaten', $first);
            $this->assertArrayHasKey('nama_kabupaten', $first);
            $this->assertArrayHasKey('nama_provinsi', $first);
            $this->assertArrayHasKey('kode_provinsi', $first);
        } else {
            $this->markTestSkipped('Tidak ada data kabupaten untuk diverifikasi.');
        }
    }

    /**
     * Filter kode_provinsi mempersempit hasil ke kecamatan di provinsi tersebut.
     */
    public function test_filter_kode_provinsi_mempersempit_hasil()
    {
        $provinsiList = Region::kabupaten()
            ->select('prov.region_code as kode_prov')
            ->groupBy('prov.region_code')
            ->limit(2)
            ->get();

        if ($provinsiList->count() < 2) {
            $this->markTestSkipped('Butuh minimal 2 provinsi untuk test ini.');
        }

        $kodeProvinsi = $provinsiList->first()->kode_prov;

        // Tanpa filter
        $tanpaFilter = $this->actingAs($this->user)
            ->get('/kabupaten?length=10', ['X-Requested-With' => 'XMLHttpRequest']);
        $totalTanpa = $tanpaFilter->json('recordsFiltered');

        // Dengan filter provinsi
        $denganFilter = $this->actingAs($this->user)
            ->get("/kabupaten?length=10&kode_provinsi={$kodeProvinsi}", [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $denganFilter->assertStatus(200);
        $totalDengan = $denganFilter->json('recordsFiltered');

        // Hasil filter harus <= total tanpa filter
        $this->assertLessThanOrEqual(
            $totalTanpa,
            $totalDengan,
            'Filter provinsi seharusnya menghasilkan data yang lebih sedikit atau sama.'
        );

        // Semua item yang dikembalikan harus berasal dari provinsi yang dipilih
        $data = $denganFilter->json('data');
        foreach ($data as $item) {
            $this->assertEquals(
                $kodeProvinsi,
                $item['kode_provinsi'],
                "Kabupaten '{$item['nama_kabupaten']}' bukan dari provinsi {$kodeProvinsi}."
            );
        }
    }

    /**
     * Filter provinsi yang tidak ada menghasilkan data kosong (bukan error).
     */
    public function test_filter_provinsi_tidak_ada_menghasilkan_data_kosong()
    {
        $response = $this->actingAs($this->user)
            ->get('/kabupaten?length=10&kode_provinsi=XX_TIDAK_ADA', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $this->assertEquals(0, $response->json('recordsFiltered'));
        $this->assertEmpty($response->json('data'));
    }

    /**
     * Aksi filter tidak mengubah data di database.
     */
    public function test_filter_tidak_mengubah_data_database()
    {
        $countBefore = Region::kabupaten()->count();

        $this->actingAs($this->user)
            ->get('/kabupaten?length=10&kode_provinsi=32', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $this->actingAs($this->user)
            ->get('/kabupaten?length=10', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $countAfter = Region::kabupaten()->count();
        $this->assertEquals($countBefore, $countAfter, 'Filter tidak boleh mengubah data database.');
    }
}
