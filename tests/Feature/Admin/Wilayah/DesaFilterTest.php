<?php

namespace Tests\Feature\Admin\Wilayah;

use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Test untuk memastikan filter 3 tingkat (provinsi → kabupaten → kecamatan)
 * pada halaman Data Wilayah > Desa berfungsi dengan baik.
 *
 * Catatan: Tabel tbl_regions level desa sangat besar (ratusan ribu baris),
 * sehingga semua test AJAX selalu menyertakan filter wilayah untuk
 * menghindari memory exhaustion saat menghitung COUNT(*).
 */
class DesaFilterTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    /** Kode wilayah Jawa Barat digunakan sebagai default filter di semua test AJAX */
    protected string $kodeProvDefault = '32';

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
    public function test_halaman_desa_dapat_diakses()
    {
        $response = $this->actingAs($this->user)->get('/desa');

        $response->assertStatus(200);
        $response->assertViewIs('admin.wilayah.desa.index');
    }

    /**
     * Halaman memerlukan autentikasi.
     */
    public function test_halaman_desa_memerlukan_autentikasi()
    {
        $response = $this->get('/desa');

        $response->assertRedirect('/login');
    }

    /**
     * View menerima variabel fillters dengan ketiga key wilayah.
     */
    public function test_view_menerima_variabel_fillters_lengkap()
    {
        $response = $this->actingAs($this->user)->get('/desa');

        $response->assertStatus(200);
        $response->assertViewHas('fillters', function ($fillters) {
            return array_key_exists('kode_provinsi', $fillters)
                && array_key_exists('kode_kabupaten', $fillters)
                && array_key_exists('kode_kecamatan', $fillters);
        });
    }

    /**
     * Halaman menampilkan komponen filter 3 tingkat.
     */
    public function test_halaman_menampilkan_komponen_filter_tiga_tingkat()
    {
        $response = $this->actingAs($this->user)->get('/desa');

        $response->assertStatus(200);

        // Tombol toggle filter
        $response->assertSee('collapse-filter', false);
        $response->assertSee('fas fa-filter', false);

        // Select2 provinsi, kabupaten, kecamatan
        $response->assertSee('id="provinsi"', false);
        $response->assertSee('id="kabupaten"', false);
        $response->assertSee('id="kecamatan"', false);

        // Tombol Cari dan Reset
        $response->assertSee('id="filter"', false);
        $response->assertSee('id="reset"', false);
    }

    /**
     * Request AJAX dengan filter provinsi mengembalikan format DataTables JSON.
     * (Selalu gunakan filter agar tidak OOM pada tabel desa yang besar)
     */
    public function test_ajax_dengan_filter_mengembalikan_format_datatable()
    {
        $response = $this->actingAs($this->user)
            ->get("/desa?length=10&kode_provinsi={$this->kodeProvDefault}", [
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
     * Response DataTables memiliki semua kolom yang benar.
     */
    public function test_ajax_mengembalikan_kolom_lengkap()
    {
        $response = $this->actingAs($this->user)
            ->get("/desa?length=5&kode_provinsi={$this->kodeProvDefault}", [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);

        $data = $response->json('data');
        if (count($data) > 0) {
            $first = $data[0];
            $this->assertArrayHasKey('kode_desa', $first);
            $this->assertArrayHasKey('nama_desa', $first);
            $this->assertArrayHasKey('kode_kecamatan', $first);
            $this->assertArrayHasKey('nama_kecamatan', $first);
            $this->assertArrayHasKey('kode_kabupaten', $first);
            $this->assertArrayHasKey('nama_kabupaten', $first);
            $this->assertArrayHasKey('kode_provinsi', $first);
            $this->assertArrayHasKey('nama_provinsi', $first);
        } else {
            $this->markTestSkipped("Tidak ada desa di provinsi {$this->kodeProvDefault}.");
        }
    }

    /**
     * Filter kode_provinsi: semua desa yang dikembalikan harus dari provinsi tersebut.
     */
    public function test_filter_kode_provinsi_mengembalikan_desa_yang_benar()
    {
        $kodeProvinsi = $this->kodeProvDefault;

        $response = $this->actingAs($this->user)
            ->get("/desa?length=10&kode_provinsi={$kodeProvinsi}", [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);

        // Harus ada data di Jawa Barat
        $this->assertGreaterThan(0, $response->json('recordsFiltered'));

        // Semua desa di page ini harus dari provinsi yang dipilih
        foreach ($response->json('data') as $item) {
            $this->assertEquals(
                $kodeProvinsi,
                $item['kode_provinsi'],
                "Desa '{$item['nama_desa']}' bukan dari provinsi {$kodeProvinsi}."
            );
        }
    }

    /**
     * Filter kode_kabupaten mempersempit hasil ke desa di kabupaten tersebut.
     */
    public function test_filter_kode_kabupaten_mempersempit_hasil()
    {
        // Ambil kabupaten dari provinsi default
        $kabupaten = Region::desa()
            ->select('kab.region_code as kode_kab', 'prov.region_code as kode_prov')
            ->where('prov.region_code', $this->kodeProvDefault)
            ->groupBy('kab.region_code', 'prov.region_code')
            ->first();

        if (! $kabupaten) {
            $this->markTestSkipped("Tidak ada data desa di provinsi {$this->kodeProvDefault}.");
        }

        // Total dengan filter provinsi saja
        $totalProvinsi = $this->actingAs($this->user)
            ->get("/desa?length=10&kode_provinsi={$kabupaten->kode_prov}", [
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->json('recordsFiltered');

        // Total dengan filter provinsi + kabupaten
        $response = $this->actingAs($this->user)
            ->get("/desa?length=10&kode_provinsi={$kabupaten->kode_prov}&kode_kabupaten={$kabupaten->kode_kab}", [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $totalKabupaten = $response->json('recordsFiltered');

        // Filter kabupaten harus menghasilkan <= filter provinsi
        $this->assertLessThanOrEqual($totalProvinsi, $totalKabupaten);

        // Semua desa harus dari kabupaten yang dipilih
        foreach ($response->json('data') as $item) {
            $this->assertEquals(
                $kabupaten->kode_kab,
                $item['kode_kabupaten'],
                "Desa '{$item['nama_desa']}' bukan dari kabupaten {$kabupaten->kode_kab}."
            );
        }
    }

    /**
     * Filter kode_kecamatan mempersempit hasil ke desa di kecamatan tersebut.
     */
    public function test_filter_kode_kecamatan_mempersempit_hasil()
    {
        $kecamatan = Region::desa()
            ->select(
                'kec.region_code as kode_kec',
                'kab.region_code as kode_kab',
                'prov.region_code as kode_prov'
            )
            ->where('prov.region_code', $this->kodeProvDefault)
            ->groupBy('kec.region_code', 'kab.region_code', 'prov.region_code')
            ->first();

        if (! $kecamatan) {
            $this->markTestSkipped("Tidak ada data desa di provinsi {$this->kodeProvDefault}.");
        }

        $response = $this->actingAs($this->user)
            ->get(
                "/desa?length=10"
                . "&kode_provinsi={$kecamatan->kode_prov}"
                . "&kode_kabupaten={$kecamatan->kode_kab}"
                . "&kode_kecamatan={$kecamatan->kode_kec}",
                ['X-Requested-With' => 'XMLHttpRequest']
            );

        $response->assertStatus(200);

        // Harus ada data
        $this->assertGreaterThan(0, $response->json('recordsFiltered'));

        // Semua desa harus dari kecamatan yang dipilih
        foreach ($response->json('data') as $item) {
            $this->assertEquals(
                $kecamatan->kode_kec,
                $item['kode_kecamatan'],
                "Desa '{$item['nama_desa']}' bukan dari kecamatan {$kecamatan->kode_kec}."
            );
        }
    }

    /**
     * Filter bertingkat: semakin dalam level, semakin sedikit atau sama hasilnya.
     */
    public function test_filter_bertingkat_semakin_mempersempit()
    {
        $kecamatan = Region::desa()
            ->select(
                'kec.region_code as kode_kec',
                'kab.region_code as kode_kab',
                'prov.region_code as kode_prov'
            )
            ->where('prov.region_code', $this->kodeProvDefault)
            ->groupBy('kec.region_code', 'kab.region_code', 'prov.region_code')
            ->first();

        if (! $kecamatan) {
            $this->markTestSkipped("Tidak ada data desa di provinsi {$this->kodeProvDefault}.");
        }

        $h = ['X-Requested-With' => 'XMLHttpRequest'];

        $totalProvinsi = $this->actingAs($this->user)
            ->get("/desa?length=10&kode_provinsi={$kecamatan->kode_prov}", $h)
            ->json('recordsFiltered');

        $totalKabupaten = $this->actingAs($this->user)
            ->get("/desa?length=10&kode_provinsi={$kecamatan->kode_prov}&kode_kabupaten={$kecamatan->kode_kab}", $h)
            ->json('recordsFiltered');

        $totalKecamatan = $this->actingAs($this->user)
            ->get("/desa?length=10&kode_provinsi={$kecamatan->kode_prov}&kode_kabupaten={$kecamatan->kode_kab}&kode_kecamatan={$kecamatan->kode_kec}", $h)
            ->json('recordsFiltered');

        $this->assertLessThanOrEqual(
            $totalProvinsi, $totalKabupaten,
            'Filter kabupaten harus ≤ filter provinsi saja.'
        );
        $this->assertLessThanOrEqual(
            $totalKabupaten, $totalKecamatan,
            'Filter kecamatan harus ≤ filter kabupaten saja.'
        );
    }

    /**
     * Filter yang tidak cocok menghasilkan data kosong (bukan error).
     */
    public function test_filter_tidak_cocok_menghasilkan_data_kosong()
    {
        $response = $this->actingAs($this->user)
            ->get('/desa?length=10&kode_provinsi=XX&kode_kabupaten=XXXXX&kode_kecamatan=XXXXXXXX', [
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
        $countBefore = Region::desa()
            ->where('prov.region_code', $this->kodeProvDefault)
            ->count();

        $filters = [
            "kode_provinsi={$this->kodeProvDefault}",
            "kode_provinsi={$this->kodeProvDefault}&kode_kabupaten=3201",
            "kode_provinsi={$this->kodeProvDefault}&kode_kabupaten=3201&kode_kecamatan=320101",
        ];

        foreach ($filters as $param) {
            $this->actingAs($this->user)
                ->get("/desa?length=10&{$param}", [
                    'X-Requested-With' => 'XMLHttpRequest',
                ])
                ->assertStatus(200);
        }

        $countAfter = Region::desa()
            ->where('prov.region_code', $this->kodeProvDefault)
            ->count();

        $this->assertEquals($countBefore, $countAfter, 'Filter tidak boleh mengubah data database.');
    }
}
