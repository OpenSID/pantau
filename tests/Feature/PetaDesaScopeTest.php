<?php

namespace Tests\Feature;

use App\Models\Desa;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Scope bersama peta desa: koordinatValid(), peta(), petaSemua().
 *
 * @see https://github.com/OpenSID/pantau/issues/750
 */
class PetaDesaScopeTest extends TestCase
{
    use DatabaseTransactions;

    private function buatDesa(array $atribut = []): Desa
    {
        return Desa::factory()->create(array_merge([
            'kode_desa' => 'T750'.fake()->unique()->numerify('######'),
            'lat' => '-7.25',
            'lng' => '112.75',
            'tgl_akses_lokal' => now()->subDays(5),
            'tgl_akses_hosting' => now()->subDays(5),
        ], $atribut));
    }

    private function idHasil($query, array $desa): array
    {
        return $query->whereIn('id', collect($desa)->pluck('id'))->pluck('id')->sort()->values()->all();
    }

    public function test_koordinat_valid_membuang_koordinat_tidak_valid_dan_desa_contoh()
    {
        $contoh = config('tracksid.desa_contoh');
        Desa::where('kode_desa', $contoh['kode_desa'])->delete();

        $valid = $this->buatDesa();
        $tidakValid = [
            $this->buatDesa(['lat' => null]),
            $this->buatDesa(['lng' => null]),
            $this->buatDesa(['lat' => 'abc']),
            $this->buatDesa(['lat' => '40.7128', 'lng' => '-74.0060']), // New York
            $this->buatDesa(['lat' => '-11', 'lng' => '112.75']),       // selatan batas RI
            $this->buatDesa(['lat' => '-7.25', 'lng' => '150']),        // timur batas RI
            $this->buatDesa(['kode_desa' => $contoh['kode_desa']]),     // kode desa contoh
            $this->buatDesa(['lat' => $contoh['lat'], 'lng' => $contoh['lng']]), // koordinat desa contoh
        ];

        $hasil = $this->idHasil(Desa::koordinatValid(), array_merge([$valid], $tidakValid));

        $this->assertSame([$valid->id], $hasil);
    }

    public function test_koordinat_valid_tidak_membuang_desa_yang_hanya_sama_satu_sumbu_dengan_desa_contoh()
    {
        $contoh = config('tracksid.desa_contoh');

        $samaLat = $this->buatDesa(['lat' => $contoh['lat'], 'lng' => '116.5']);
        $samaLng = $this->buatDesa(['lat' => '-8.6', 'lng' => $contoh['lng']]);

        $hasil = $this->idHasil(Desa::koordinatValid(), [$samaLat, $samaLng]);

        $this->assertSame(collect([$samaLat->id, $samaLng->id])->sort()->values()->all(), $hasil);
    }

    public function test_peta_hanya_menampilkan_desa_yang_mengakses_dalam_60_hari()
    {
        $aktif = $this->buatDesa();
        $lama = $this->buatDesa([
            'tgl_akses_lokal' => now()->subDays(90),
            'tgl_akses_hosting' => now()->subDays(90),
        ]);
        $koordinatSalah = $this->buatDesa(['lat' => '40.7128', 'lng' => '-74.0060']);

        $hasil = $this->idHasil(Desa::peta(), [$aktif, $lama, $koordinatSalah]);

        $this->assertSame([$aktif->id], $hasil);
    }

    public function test_peta_semua_tidak_membatasi_tanggal_akses()
    {
        $aktif = $this->buatDesa();
        $lama = $this->buatDesa([
            'tgl_akses_lokal' => now()->subDays(90),
            'tgl_akses_hosting' => now()->subDays(90),
        ]);
        $koordinatSalah = $this->buatDesa(['lat' => '40.7128', 'lng' => '-74.0060']);

        $hasil = $this->idHasil(Desa::petaSemua(), [$aktif, $lama, $koordinatSalah]);

        $this->assertSame(collect([$aktif->id, $lama->id])->sort()->values()->all(), $hasil);
    }
}
