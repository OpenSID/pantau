<?php

namespace Tests\Feature\Website;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Test filter wilayah pada dasbor publik (api/web/summary & api/web/summary-aktif).
 *
 * Memakai kode wilayah fiktif (provinsi 99) agar hasil filter tidak tercampur data lain.
 *
 * Struktur data uji:
 * - Kabupaten 99.98: kecamatan 99.98.01 (2 desa) dan 99.98.02 (1 desa), terdaftar sebagai OpenKab.
 * - Kabupaten 99.97: kecamatan 99.97.01 (1 desa).
 */
class WebsiteDashboardFilterTest extends TestCase
{
    use DatabaseTransactions;

    private string $period;

    protected function setUp(): void
    {
        parent::setUp();

        $hariIni = now()->format('Y-m-d');
        $this->period = "{$hariIni} - {$hariIni}";

        $this->seedWilayah();
    }

    public function test_filter_provinsi()
    {
        $this->assertSummary(['kode_provinsi' => '99'], [
            'desa' => 4, 'openkab' => 1, 'opensid' => 4, 'opendk' => 3, 'layanandesa' => 3, 'keloladesa' => 2, 'pbb' => 1,
        ], ['Desa Uji A1', 'Desa Uji A2', 'Desa Uji B1', 'Desa Uji C1']);
    }

    public function test_filter_kabupaten()
    {
        $this->assertSummary(['kode_provinsi' => '99', 'kode_kabupaten' => '99.98'], [
            'desa' => 3, 'openkab' => 1, 'opensid' => 3, 'opendk' => 2, 'layanandesa' => 2, 'keloladesa' => 1, 'pbb' => 1,
        ], ['Desa Uji A1', 'Desa Uji A2', 'Desa Uji B1']);
    }

    public function test_filter_kecamatan()
    {
        $this->assertSummary(['kode_provinsi' => '99', 'kode_kabupaten' => '99.98', 'kode_kecamatan' => '99.98.01'], [
            'desa' => 2, 'openkab' => 1, 'opensid' => 2, 'opendk' => 1, 'layanandesa' => 1, 'keloladesa' => 1, 'pbb' => 1,
        ], ['Desa Uji A1', 'Desa Uji A2']);
    }

    public function test_parameter_lama_tetap_diterima()
    {
        $baru = ['kode_provinsi' => '99', 'kode_kabupaten' => '99.98', 'kode_kecamatan' => '99.98.01'];
        $lama = ['provinsi' => '99', 'kabupaten' => '99.98', 'kecamatan' => '99.98.01'];

        $this->assertSame(
            $this->getJson($this->url('api/web/summary', $baru))->json(),
            $this->getJson($this->url('api/web/summary', $lama))->json()
        );
        $this->assertSame(
            $this->getJson($this->url('api/web/summary-aktif', $baru))->json(),
            $this->getJson($this->url('api/web/summary-aktif', $lama))->json()
        );
    }

    public function test_tanpa_filter_tetap_berjalan()
    {
        $this->getJson($this->url('api/web/summary', []))
            ->assertOk()
            ->assertJsonStructure(['total' => ['provinsi', 'kabupaten', 'kecamatan', 'desa'], 'detail', 'summary']);

        $this->getJson($this->url('api/web/summary-aktif', []))
            ->assertOk()
            ->assertJsonStructure(['openkab', 'opensid', 'opendk', 'layanandesa', 'keloladesa', 'pbb']);
    }

    private function assertSummary(array $filter, array $expected, array $desaAktif): void
    {
        $summary = $this->getJson($this->url('api/web/summary', $filter))->assertOk()->json();

        $this->assertSame($expected['desa'], (int) $summary['total']['desa']['total']);
        $this->assertSame($expected['openkab'], $summary['summary']['openkab']);
        $this->assertSame($expected['opensid'], $summary['summary']['opensid']);
        $this->assertSame($expected['opendk'], $summary['summary']['opendk']);
        $this->assertSame($expected['layanandesa'], $summary['summary']['layanandesa']);
        $this->assertSame($expected['keloladesa'], $summary['summary']['keloladesa']);

        $this->assertEqualsCanonicalizing($desaAktif, $summary['detail']['opensid']);
        $this->assertCount($expected['opendk'], $summary['detail']['opendk']);
        $this->assertCount($expected['layanandesa'], $summary['detail']['layanandesa']);
        $this->assertCount($expected['keloladesa'], $summary['detail']['keloladesa']);

        $aktif = $this->getJson($this->url('api/web/summary-aktif', $filter))->assertOk()->json();

        $this->assertSame([
            'openkab' => $expected['openkab'],
            'opensid' => $expected['opensid'],
            'opendk' => $expected['opendk'],
            'layanandesa' => $expected['layanandesa'],
            'keloladesa' => $expected['keloladesa'],
            'pbb' => $expected['pbb'],
        ], $aktif);
    }

    private function url(string $path, array $filter): string
    {
        return $path.'?'.http_build_query(['period' => $this->period] + $filter);
    }

    private function seedWilayah(): void
    {
        $dibuat = now()->subDay();
        $diakses = now();

        $desa = [
            ['kode_desa' => '99.98.01.2001', 'nama_desa' => 'Desa Uji A1', 'kecamatan' => '99.98.01'],
            ['kode_desa' => '99.98.01.2002', 'nama_desa' => 'Desa Uji A2', 'kecamatan' => '99.98.01'],
            ['kode_desa' => '99.98.02.2001', 'nama_desa' => 'Desa Uji B1', 'kecamatan' => '99.98.02'],
            ['kode_desa' => '99.97.01.2001', 'nama_desa' => 'Desa Uji C1', 'kecamatan' => '99.97.01'],
        ];

        foreach ($desa as $item) {
            $kabupaten = substr($item['kecamatan'], 0, 5);

            DB::table('desa')->insert([
                'kode_desa' => $item['kode_desa'],
                'nama_desa' => $item['nama_desa'],
                'kode_kecamatan' => $item['kecamatan'],
                'nama_kecamatan' => 'Kecamatan '.$item['kecamatan'],
                'kode_kabupaten' => $kabupaten,
                'nama_kabupaten' => 'Kabupaten '.$kabupaten,
                'kode_provinsi' => '99',
                'nama_provinsi' => 'Provinsi Uji',
                'alamat_kantor' => '-',
                'jml_surat_tte' => 0,
                'modul_tte' => 0,
                'tgl_akses_hosting' => $diakses,
                'created_at' => $dibuat,
                'updated_at' => $diakses,
            ]);
        }

        foreach (['99.98.01', '99.98.02', '99.97.01'] as $kecamatan) {
            $kabupaten = substr($kecamatan, 0, 5);

            DB::table('opendk')->insert([
                'kode_kecamatan' => $kecamatan,
                'nama_kecamatan' => 'Kecamatan '.$kecamatan,
                'kode_kabupaten' => $kabupaten,
                'nama_kabupaten' => 'Kabupaten '.$kabupaten,
                'kode_provinsi' => '99',
                'nama_provinsi' => 'Provinsi Uji',
                'url' => "https://{$kecamatan}.opendk.test",
                'created_at' => $dibuat,
                'updated_at' => $diakses,
            ]);
        }

        foreach (['99.98.01.2001', '99.98.02.2001', '99.97.01.2001'] as $kodeDesa) {
            DB::table('track_mobile')->insert([
                'id' => (string) Str::uuid(),
                'kode_desa' => $kodeDesa,
                'tgl_akses' => $diakses->format('Y-m-d'),
                'created_at' => $dibuat,
                'updated_at' => $diakses,
            ]);
        }

        foreach (['99.98.01.2001', '99.97.01.2001'] as $kodeDesa) {
            DB::table('track_keloladesa')->insert([
                'id_device' => (string) Str::uuid(),
                'kode_desa' => $kodeDesa,
                'tgl_akses' => $diakses->format('Y-m-d'),
                'created_at' => $dibuat,
                'updated_at' => $diakses,
            ]);
        }

        DB::table('pbb')->insert([
            'kode_desa' => '99.98.01.2001',
            'nama_desa' => 'Desa Uji A1',
            'kode_kecamatan' => '99.98.01',
            'nama_kecamatan' => 'Kecamatan 99.98.01',
            'kode_kabupaten' => '99.98',
            'nama_kabupaten' => 'Kabupaten 99.98',
            'kode_provinsi' => '99',
            'nama_provinsi' => 'Provinsi Uji',
            'versi' => '2407.0.0',
            'created_at' => $dibuat,
            'updated_at' => $diakses,
        ]);

        DB::table('openkab')->insert([
            'kode_kab' => '99.98',
            'nama_kab' => 'Kabupaten 99.98',
            'kode_prov' => '99',
            'nama_prov' => 'Provinsi Uji',
            'nama_aplikasi' => 'OpenKab',
            'sebutan_kab' => 'Kabupaten',
            'url' => 'https://99-98.openkab.test',
            'versi' => '2407.0.0',
            'created_at' => $dibuat,
            'updated_at' => $diakses,
        ]);
    }
}
