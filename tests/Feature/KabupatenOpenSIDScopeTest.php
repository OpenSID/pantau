<?php

namespace Tests\Feature;

use App\Models\Desa;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Test untuk memastikan scope kabupatenOpenSID dengan filter status 3 (premium)
 * tidak membocorkan baris di luar kondisi wilayah, serta mencakup versi premium
 * hosting maupun lokal.
 */
class KabupatenOpenSIDScopeTest extends TestCase
{
    use DatabaseTransactions;

    private const VERSI_PREMIUM = '2611.0.0';

    protected function setUp(): void
    {
        parent::setUp();

        // Hindari request GitHub sungguhan: rilis premium dipalsukan dan cache di-set
        Http::fake([
            'https://api.github.com/repos/OpenSID/rilis-premium/releases/latest' => Http::response([
                'tag_name' => 'v'.self::VERSI_PREMIUM,
            ]),
        ]);
        Cache::forever('opensid_premium_version', self::VERSI_PREMIUM);
    }

    public function test_filter_premium_tidak_membocorkan_desa_tanpa_kode_kabupaten()
    {
        // Kabupaten 1201: desa dengan versi premium lokal (harus tampil)
        $this->buatDesa('1201001001', '1201', 'Kabupaten Premium', self::VERSI_PREMIUM.'-premium', null);

        // Kabupaten 1202: desa versi umum (tidak boleh tampil)
        $this->buatDesa('1202001001', '1202', 'Kabupaten Umum', '22.06', null);

        // Desa tanpa kode kabupaten dengan versi premium: tidak boleh bocor ke hasil
        $this->buatDesa('1299001001', '', '', self::VERSI_PREMIUM.'-premium', null);

        $hasil = Desa::kabupatenOpenSID(['status' => 3])->get();

        $this->assertCount(1, $hasil);
        $this->assertSame('1201', $hasil->first()->kode_kabupaten);
        $this->assertSame(1, (int) $hasil->first()->offline);

        // Versi premium di-resolve sekali per pemanggilan scope, tidak per subquery
        Http::assertSentCount(1);
    }

    public function test_filter_premium_menyertakan_versi_premium_hosting()
    {
        // Kabupaten 1204: versi premium hanya di hosting (harus tampil sebagai online)
        $this->buatDesa('1204001001', '1204', 'Kabupaten Hosting Premium', null, self::VERSI_PREMIUM.'-premium');

        // Kabupaten 1205: versi umum di hosting (tidak boleh tampil)
        $this->buatDesa('1205001001', '1205', 'Kabupaten Hosting Umum', null, '22.06');

        $hasil = Desa::kabupatenOpenSID(['status' => 3])->get();

        $this->assertCount(1, $hasil);
        $this->assertSame('1204', $hasil->first()->kode_kabupaten);
        $this->assertSame(1, (int) $hasil->first()->online);
    }

    public function test_filter_status_online_dan_offline_tidak_error()
    {
        // Kabupaten 1206: hanya hosting (online)
        $this->buatDesa('1206001001', '1206', 'Kabupaten Online', null, '22.06');

        // Kabupaten 1207: hanya lokal (offline)
        $this->buatDesa('1207001001', '1207', 'Kabupaten Offline', '22.06', null);

        // Tabel tidak kosong (ada data lain), jadi hanya kabupaten fixture yang diperiksa
        $kodeFixture = ['1206', '1207'];

        $online = Desa::kabupatenOpenSID(['status' => 1])->get()
            ->pluck('kode_kabupaten')
            ->intersect($kodeFixture)
            ->values()
            ->all();
        $this->assertSame(['1206'], $online);

        $offline = Desa::kabupatenOpenSID(['status' => 2])->get()
            ->pluck('kode_kabupaten')
            ->intersect($kodeFixture)
            ->values()
            ->all();
        $this->assertSame(['1207'], $offline);
    }

    private function buatDesa(string $kodeDesa, string $kodeKabupaten, string $namaKabupaten, ?string $versiLokal, ?string $versiHosting): void
    {
        Desa::create([
            'nama_desa' => 'Desa '.$kodeDesa,
            'kode_desa' => $kodeDesa,
            'kode_provinsi' => '12',
            'kode_kabupaten' => $kodeKabupaten,
            'kode_kecamatan' => substr($kodeDesa, 0, 6),
            'nama_kabupaten' => $namaKabupaten,
            'nama_provinsi' => 'Provinsi Dua Belas',
            'versi_lokal' => $versiLokal,
            'versi_hosting' => $versiHosting,
            'tgl_akses_lokal' => now()->subDays(3),
        ]);
    }
}
