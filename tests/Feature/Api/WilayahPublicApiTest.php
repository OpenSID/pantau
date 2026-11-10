<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * API wilayah (api/v1/wilayah/*) bersifat publik: dapat diakses tanpa token
 * agar filter select2 di halaman publik berfungsi, tanpa perlu menanam
 * token Sanctum di HTML.
 */
class WilayahPublicApiTest extends TestCase
{
    use DatabaseTransactions;

    /** @var object Satu baris wilayah nyata dari view `kode_wilayah`. */
    private $wilayah;

    protected function setUp(): void
    {
        parent::setUp();

        // `kode_wilayah` adalah VIEW database (tidak bisa di-insert), jadi pakai data yang ada.
        $this->wilayah = DB::table('kode_wilayah')->whereNotNull('kode_desa')->first();

        if (! $this->wilayah) {
            $this->markTestSkipped('Data kode_wilayah kosong.');
        }
    }

    public function test_provinsi_dapat_diakses_tanpa_token()
    {
        $this->getJson('api/v1/wilayah/provinsi?kode_prov=' . $this->wilayah->kode_prov)
            ->assertOk()
            ->assertJsonStructure(['data', 'current_page', 'last_page'])
            ->assertJsonFragment([
                'kode_prov' => $this->wilayah->kode_prov,
                'nama_prov' => $this->wilayah->nama_prov,
            ]);
    }

    public function test_kabupaten_kecamatan_desa_dapat_diakses_tanpa_token()
    {
        $w = $this->wilayah;

        $kabupaten = $this->getJson('api/v1/wilayah/kabupaten?kode_prov=' . $w->kode_prov)
            ->assertOk()
            ->assertJsonFragment(['kode_kab' => $w->kode_kab])
            ->json('data');

        // Filter kode_prov benar-benar membatasi hasil.
        foreach ($kabupaten as $kab) {
            $this->assertStringStartsWith($w->kode_prov, $kab['kode_kab']);
        }

        $this->getJson('api/v1/wilayah/kecamatan?kode_kab=' . $w->kode_kab . '&kode_kec=' . $w->kode_kec)
            ->assertOk()
            ->assertJsonFragment(['kode_kec' => $w->kode_kec]);

        $this->getJson('api/v1/wilayah/desa?kode_kec=' . $w->kode_kec . '&kode_desa=' . $w->kode_desa)
            ->assertOk()
            ->assertJsonFragment(['kode_desa' => $w->kode_desa]);

        $this->getJson('api/v1/wilayah?kode_desa=' . $w->kode_desa)
            ->assertOk()
            ->assertJsonFragment(['nama_desa' => $w->nama_desa]);
    }

    public function test_klien_lama_yang_mengirim_token_tetap_berfungsi()
    {
        $this->withHeaders(['Authorization' => 'Bearer token-sembarang'])
            ->getJson('api/v1/wilayah/provinsi')
            ->assertOk();
    }

    public function test_per_page_dibatasi_maksimal_100()
    {
        $this->getJson('api/v1/wilayah/desa?per_page=100')->assertOk();

        $this->getJson('api/v1/wilayah/desa?per_page=101')
            ->assertStatus(422)
            ->assertJsonValidationErrors('per_page');
    }

    public function test_endpoint_track_tetap_wajib_token()
    {
        $this->postJson('api/v1/track/desa', [])->assertUnauthorized();
        $this->postJson('api/v1/track/opendk', [])->assertUnauthorized();
        $this->postJson('api/v1/track/openkab', [])->assertUnauthorized();
        $this->getJson('api/v1/track/mobile')->assertUnauthorized();
    }

    public function test_halaman_publik_tidak_memuat_token_bearer()
    {
        // Walaupun ada yang masih mengisi token lama, tidak boleh muncul di HTML.
        config(['tracksid.sandi.sanctum_token' => 'TOKEN-RAHASIA-UJI']);

        foreach (['/web', '/web/opendk'] as $url) {
            $response = $this->get($url);

            $response->assertOk();
            $response->assertDontSee('TOKEN-RAHASIA-UJI', false);
            $response->assertDontSee('Bearer', false);
            $response->assertSee('api/v1/wilayah/provinsi', false);
        }
    }
}
