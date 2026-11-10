<?php

namespace Tests\Feature\Website;

use App\Models\Desa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Endpoint peta desa publik dan admin: validasi marker, rentang tanggal, dan escaping popup.
 *
 * @see https://github.com/OpenSID/pantau/issues/750
 */
class DataPetaTest extends TestCase
{
    use DatabaseTransactions;

    /** Rentang tanggal lampau agar tidak bercampur dengan data yang sudah ada. */
    private const PERIODE = '2001-02-03 - 2001-02-04';

    private function buatDesa(array $atribut = []): Desa
    {
        return Desa::factory()->create(array_merge([
            'kode_desa' => 'T750'.fake()->unique()->numerify('######'),
            'lat' => '-7.25',
            'lng' => '112.75',
            'created_at' => '2001-02-03 08:00:00',
            'tgl_akses_lokal' => now()->subDays(5),
            'tgl_akses_hosting' => now()->subDays(5),
        ], $atribut));
    }

    private function features(string $url, array $query = []): array
    {
        return $this->getJson($url.'?'.http_build_query($query), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('type', 'FeatureCollection')
            ->json('features');
    }

    private function idFeatures(array $features): array
    {
        return collect($features)->pluck('id')->filter()->all();
    }

    public function test_data_peta_membuang_desa_contoh_dan_koordinat_di_luar_ri()
    {
        $contoh = config('tracksid.desa_contoh');

        $valid = $this->buatDesa();
        $luarRi = $this->buatDesa(['lat' => '40.7128', 'lng' => '-74.0060']);
        $desaContoh = $this->buatDesa(['lat' => $contoh['lat'], 'lng' => $contoh['lng']]);

        $ids = $this->idFeatures($this->features('/web/data-peta', ['period' => self::PERIODE]));

        $this->assertContains($valid->id, $ids);
        $this->assertNotContains($luarRi->id, $ids);
        $this->assertNotContains($desaContoh->id, $ids);
    }

    public function test_data_peta_menyertakan_desa_pada_tanggal_akhir_periode()
    {
        $hariTerakhir = $this->buatDesa(['created_at' => '2001-02-04 15:30:00']);
        $diLuarPeriode = $this->buatDesa(['created_at' => '2001-02-05 00:00:01']);

        $ids = $this->idFeatures($this->features('/web/data-peta', ['period' => self::PERIODE]));

        $this->assertContains($hariTerakhir->id, $ids);
        $this->assertNotContains($diLuarPeriode->id, $ids);
    }

    public function test_data_peta_mengembalikan_id_dan_popup_seragam()
    {
        $desa = $this->buatDesa(['nama_desa' => 'sukamaju', 'nama_kecamatan' => 'kec uji']);

        $feature = collect($this->features('/web/data-peta', ['period' => self::PERIODE]))->firstWhere('id', $desa->id);

        $this->assertNotNull($feature);
        $this->assertSame([112.75, -7.25], $feature['geometry']['coordinates']);
        $this->assertStringContainsString('Sukamaju', $feature['properties']['popupContent']);
        $this->assertStringContainsString('Kecamatan', $feature['properties']['popupContent']);
        $this->assertStringContainsString('Kec Uji', $feature['properties']['popupContent']);
    }

    public function test_popup_meng_escape_data_desa()
    {
        $payload = '<img src=x onerror=alert(1)>';
        $desa = $this->buatDesa(['alamat_kantor' => $payload, 'nama_desa' => '<script>alert(1)</script>']);

        $feature = collect($this->features('/web/data-peta', ['period' => self::PERIODE]))->firstWhere('id', $desa->id);
        $popup = $feature['properties']['popupContent'];

        $this->assertStringNotContainsString('<img', $popup);
        $this->assertStringNotContainsString('<script', $popup);
        $this->assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $popup);
    }

    public function test_popup_tamu_tidak_menampilkan_website_tetapi_admin_menampilkan()
    {
        $desa = $this->buatDesa(['url_hosting' => 'sukamaju-uji.desa.id']);

        $popupTamu = collect($this->features('/peta'))->firstWhere('id', $desa->id)['properties']['popupContent'];
        $this->assertStringNotContainsString('sukamaju-uji.desa.id', $popupTamu);

        $this->actingAs(User::factory()->create());
        $popupAdmin = collect($this->features('/peta'))->firstWhere('id', $desa->id)['properties']['popupContent'];
        $this->assertStringContainsString('sukamaju-uji.desa.id', $popupAdmin);
        $this->assertStringContainsString('rel="noopener noreferrer"', $popupAdmin);
    }

    public function test_peta_opensid_publik_membuang_koordinat_di_luar_ri()
    {
        $valid = $this->buatDesa();
        $luarRi = $this->buatDesa(['lat' => '40.7128', 'lng' => '-74.0060']);

        $ids = $this->idFeatures($this->features('/web/opensid/peta', ['period' => self::PERIODE]));

        $this->assertContains($valid->id, $ids);
        $this->assertNotContains($luarRi->id, $ids);
    }
}
