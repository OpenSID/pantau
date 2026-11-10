<?php

namespace Tests\Feature;

use App\Models\Desa;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Memastikan definisi status server desa (issue #748) konsisten di semua laporan:
 * - Online: versi hosting terisi
 * - Offline: versi lokal terisi dan belum ada versi hosting
 * - Desa dengan dua versi masuk Online saja, dan tidak dihitung ganda
 * - Desa tanpa versi tidak masuk Online maupun Offline
 */
class DefinisiStatusServerTest extends TestCase
{
    use DatabaseTransactions;

    private const KODE_KABUPATEN = '1208';

    protected function setUp(): void
    {
        parent::setUp();

        // 01 hanya hosting, 02 hanya lokal, 03 keduanya, 04 tidak ada versi
        $this->buatDesa('1208001001', '99.01', null);
        $this->buatDesa('1208001002', null, '99.02');
        $this->buatDesa('1208001003', '99.03', '99.04');
        $this->buatDesa('1208001004', null, null);
    }

    public function test_scope_online_dan_offline_memakai_definisi_yang_sama()
    {
        $online = Desa::where('kode_kabupaten', self::KODE_KABUPATEN)->hostingOnline()->pluck('kode_desa')->sort()->values()->all();
        $offline = Desa::where('kode_kabupaten', self::KODE_KABUPATEN)->hostingOffline()->pluck('kode_desa')->sort()->values()->all();

        $this->assertSame(['1208001001', '1208001003'], $online);
        $this->assertSame(['1208001002'], $offline);
    }

    public function test_agregat_kabupaten_tidak_menghitung_desa_dengan_dua_versi_ke_offline()
    {
        $baris = Desa::kabupatenOpenSID(['status' => null])->get()
            ->firstWhere('kode_kabupaten', self::KODE_KABUPATEN);

        $this->assertNotNull($baris);
        $this->assertSame(2, (int) $baris->online);
        $this->assertSame(1, (int) $baris->offline);
    }

    public function test_versi_opensid_menghitung_setiap_desa_sekali()
    {
        $fixture = ['99.01', '99.02', '99.03', '99.04'];

        $baris = Desa::versiOpenSID(['aktif' => null])->get()
            ->filter(fn ($row) => in_array($row->versi, $fixture, true));

        // Tiga desa (01, 02, 03) masing-masing sekali. Desa 03 hanya tercatat sebagai online dengan versi hosting.
        $this->assertSame(3, (int) $baris->sum('online') + (int) $baris->sum('offline'));
        $this->assertSame(0, (int) $baris->where('versi', '99.04')->sum('offline') + (int) $baris->where('versi', '99.04')->sum('online'));
    }

    public function test_jumlah_desa_dashboard_memakai_definisi_yang_sama()
    {
        $aktifSejak = 'DATE(now() - interval 29 day)';
        $aktifDalamPeriode = "coalesce(tgl_akses_lokal, '1970-01-01 00:00:00') between ? and ?";

        $jumlah = Desa::jumlahDesa()->first();

        // Kartu dashboard: online dan offline hanya desa aktif 29 hari terakhir
        $aktifOnline = Desa::hostingOnline()
            ->whereRaw("greatest(coalesce(tgl_akses_lokal, '1970-01-01 00:00:00'), coalesce(tgl_akses_hosting, '1970-01-01 00:00:00')) >= {$aktifSejak}")
            ->count();
        $aktifOffline = Desa::hostingOffline()
            ->whereRaw("coalesce(tgl_akses_lokal, '1970-01-01 00:00:00') >= {$aktifSejak}")
            ->count();

        $this->assertSame($aktifOnline, (int) $jumlah->desa_online);
        $this->assertSame($aktifOffline, (int) $jumlah->desa_offline);

        // Jumlah kabupaten online dan offline memakai definisi yang sama
        $this->assertSame(
            Desa::hostingOnline()->distinct('kode_kabupaten')->count('kode_kabupaten'),
            (int) $jumlah->kabupaten_online
        );
        $this->assertSame(
            Desa::hostingOffline()->distinct('kode_kabupaten')->count('kode_kabupaten'),
            (int) $jumlah->kabupaten_offline
        );

        // Filter periode: desa aktif pada rentang tanggal
        $mulai = now()->subDays(7)->format('Y-m-d');
        $selesai = now()->format('Y-m-d');
        request()->merge(['period' => $mulai.' - '.$selesai]);

        $periode = Desa::jumlahDesa()->first();
        $offlinePeriode = Desa::hostingOffline()
            ->whereRaw($aktifDalamPeriode, [$mulai.' 00:00:00', $selesai.' 23:59:59'])
            ->count();

        $this->assertSame($offlinePeriode, (int) $periode->desa_offline);
    }

    private function buatDesa(string $kodeDesa, ?string $versiHosting, ?string $versiLokal): void
    {
        Desa::create([
            'nama_desa' => 'Desa '.$kodeDesa,
            'kode_desa' => $kodeDesa,
            'kode_provinsi' => '12',
            'kode_kabupaten' => self::KODE_KABUPATEN,
            'kode_kecamatan' => '120801',
            'nama_kabupaten' => 'Kabupaten Definisi',
            'nama_provinsi' => 'Provinsi Dua Belas',
            'versi_lokal' => $versiLokal,
            'versi_hosting' => $versiHosting,
            'tgl_akses_lokal' => now()->subDays(3),
            'tgl_akses_hosting' => $versiHosting ? now()->subDays(3) : null,
        ]);
    }
}
