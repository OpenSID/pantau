<?php

namespace Tests\Feature\Website;

use App\Models\Desa;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Issue #744: angka kartu "Desa Aktif" di dasbor publik harus sama dengan
 * jumlah baris pada halaman tujuan tautannya (web/opensid-data).
 */
class DesaAktifOpensidLinkTest extends TestCase
{
    use DatabaseTransactions;

    private string $kodeProvinsi;

    private string $kodeKabupaten;

    protected function setUp(): void
    {
        parent::setUp();

        // Wilayah unik agar data uji terisolasi dari data lain di database
        $this->kodeProvinsi = '9'.random_int(1000000, 9999999);
        $this->kodeKabupaten = $this->kodeProvinsi.random_int(10, 99);

        $this->buatDesa(3, now()->subDays(2));   // aktif 7 hari & 30 hari
        $this->buatDesa(2, now()->subDays(15));  // aktif 30 hari saja
        $this->buatDesa(1, now()->subDays(40));  // di luar 30 hari
        $this->buatDesa(1, now()->subDays(45));  // di luar 30 hari
    }

    private function buatDesa(int $jumlah, $tglAkses): void
    {
        Desa::factory()->count($jumlah)->create([
            'kode_provinsi' => $this->kodeProvinsi,
            'kode_kabupaten' => $this->kodeKabupaten,
            'tgl_akses_lokal' => $tglAkses,
            'tgl_akses_hosting' => null,
        ]);
    }

    private function periode(int $hariAwal, int $hariAkhir): string
    {
        return now()->subDays($hariAwal)->format('Y-m-d').' - '.now()->subDays($hariAkhir)->format('Y-m-d');
    }

    private function angkaKartu(array $params): int
    {
        $response = $this->getJson('/api/web/desa-aktif-opensid?'.http_build_query($params));
        $response->assertOk();

        return (int) $response->json('aktif');
    }

    private function jumlahBarisTabel(array $params): int
    {
        $response = $this->get(
            '/datatables/pengguna-opensid?'.http_build_query(array_merge(['draw' => 1, 'start' => 0, 'length' => 1], $params)),
            ['X-Requested-With' => 'XMLHttpRequest']
        );
        $response->assertOk();

        return (int) $response->json('recordsFiltered');
    }

    public function test_periode_default_sama_dengan_filter_akses_satu_bulan()
    {
        $wilayah = ['kode_provinsi' => $this->kodeProvinsi, 'kode_kabupaten' => $this->kodeKabupaten];

        $kartu = $this->angkaKartu($wilayah + ['period' => $this->periode(29, 0)]);

        $this->assertSame(5, $kartu);
        $this->assertSame($kartu, $this->jumlahBarisTabel($wilayah + ['akses' => 6]));
    }

    public function test_filter_akses_tujuh_hari_lama_tidak_sama_dengan_kartu()
    {
        // Mendokumentasikan bug sebelum perbaikan: tautan lama memakai akses=4 (7 hari)
        $wilayah = ['kode_provinsi' => $this->kodeProvinsi, 'kode_kabupaten' => $this->kodeKabupaten];

        $kartu = $this->angkaKartu($wilayah + ['period' => $this->periode(29, 0)]);
        $tabelLama = $this->jumlahBarisTabel($wilayah + ['akses' => 4]);

        $this->assertSame(3, $tabelLama);
        $this->assertNotSame($kartu, $tabelLama);
    }

    public function test_periode_kustom_diteruskan_ke_tabel_menghasilkan_jumlah_sama()
    {
        $wilayah = ['kode_provinsi' => $this->kodeProvinsi, 'kode_kabupaten' => $this->kodeKabupaten];
        $period = $this->periode(50, 35);

        $kartu = $this->angkaKartu($wilayah + ['period' => $period]);

        $this->assertSame(2, $kartu);
        $this->assertSame($kartu, $this->jumlahBarisTabel($wilayah + ['period' => $period]));
    }

    public function test_periode_tidak_valid_diabaikan_oleh_tabel()
    {
        $wilayah = ['kode_provinsi' => $this->kodeProvinsi, 'kode_kabupaten' => $this->kodeKabupaten];

        foreach (['abc', '2025-13-01 - 2025-12-31', '2025-02-01 - 2025-01-01', "2025-01-01' OR 1=1 -- - 2025-01-31"] as $period) {
            $this->assertSame(7, $this->jumlahBarisTabel($wilayah + ['period' => $period]), "period: {$period}");
        }
    }

    public function test_periode_tidak_valid_pada_kartu_kembali_ke_default_tiga_puluh_hari()
    {
        $wilayah = ['kode_provinsi' => $this->kodeProvinsi, 'kode_kabupaten' => $this->kodeKabupaten];

        $this->assertSame(5, $this->angkaKartu($wilayah + ['period' => 'abc']));
    }

    public function test_parse_periode()
    {
        $this->assertSame(['2025-01-01 00:00:00', '2025-01-31 23:59:59'], Desa::parsePeriode('2025-01-01 - 2025-01-31'));
        $this->assertSame(['2025-01-01 00:00:00', '2025-01-01 23:59:59'], Desa::parsePeriode('2025-01-01 - 2025-01-01'));
        $this->assertNull(Desa::parsePeriode(null));
        $this->assertNull(Desa::parsePeriode(''));
        $this->assertNull(Desa::parsePeriode('2025-01-01'));
        $this->assertNull(Desa::parsePeriode('2025-02-30 - 2025-03-01'));
        $this->assertNull(Desa::parsePeriode('2025-03-01 - 2025-02-01'));
        $this->assertNull(Desa::parsePeriode(['2025-01-01', '2025-01-31']));
    }

    public function test_halaman_data_menampilkan_periode_valid_dan_menolak_yang_tidak_valid()
    {
        $period = $this->periode(50, 35);

        $this->get('/web/opensid-data?period='.urlencode($period))
            ->assertOk()
            ->assertSee('let periodeAkses = '.json_encode($period), false);

        $this->get('/web/opensid-data?period='.urlencode('<script>alert(1)</script>'))
            ->assertOk()
            ->assertSee('let periodeAkses = null', false);
    }

    public function test_dasbor_tidak_lagi_memakai_akses_tujuh_hari_pada_tautan_kartu()
    {
        $this->get('/web/opensid')
            ->assertOk()
            ->assertDontSee('params.akses = 4', false)
            ->assertSee('linkParams.akses = 6', false);
    }
}
