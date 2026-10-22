<?php

namespace Tests\Feature\Api;

use App\Models\Desa;
use App\Models\DomainHistory;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;

class TrackDomainHistoryTest extends TracksidApiTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    /**
     * Remove dots from wilayah code for request.
     */
    protected function formatKodeForRequest($kode)
    {
        return str_replace('.', '', $kode);
    }

    /**
     * Bersihkan state domain desa agar pengujian tidak bergantung data lama.
     */
    protected function resetDomainState(string $kodeDesa): void
    {
        DomainHistory::where('kode_desa', $kodeDesa)->delete();
        Desa::where('kode_desa', $kodeDesa)->update([
            'domain' => null,
            'domain_updated_at' => null,
            'url_hosting' => null,
            'url_lokal' => null,
        ]);
    }

    /**
     * Bangun payload tracking OpenSID desa.
     */
    protected function payload(Wilayah $wilayah, string $url): array
    {
        return [
            'nama_desa' => $wilayah->nama_desa,
            'kode_desa' => $this->formatKodeForRequest($wilayah->kode_desa),
            'kode_pos' => '23130',
            'nama_kecamatan' => $wilayah->nama_kec,
            'kode_kecamatan' => $this->formatKodeForRequest($wilayah->kode_kec),
            'nama_kabupaten' => $wilayah->nama_kab,
            'kode_kabupaten' => $this->formatKodeForRequest($wilayah->kode_kab),
            'nama_provinsi' => $wilayah->nama_prov,
            'kode_provinsi' => $this->formatKodeForRequest($wilayah->kode_prov),
            'lat' => '-0.789',
            'lng' => '113.456',
            'alamat_kantor' => 'Alamat Kantor Testing',
            'url' => $url,
            'ip_address' => '192.168.1.120',
            'version' => '24.01.1',
        ];
    }

    public function test_domain_pertama_dicatat_sebagai_riwayat_aktif()
    {
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah);

        $this->resetDomainState($wilayah->kode_desa);

        $response = $this->postJsonWithToken('/api/track/desa', $this->payload($wilayah, 'https://desa-pertama.example.id'));
        $response->assertStatus(200);

        $desa = Desa::where('kode_desa', $wilayah->kode_desa)->first();

        $this->assertNotNull($desa);
        $this->assertDatabaseHas('domain_histories', [
            'desa_id' => $desa->id,
            'kode_desa' => $wilayah->kode_desa,
            'domain_normalized' => 'desa-pertama.example.id',
            'is_active' => true,
            'source' => 'crawler',
        ]);
        $this->assertSame('desa-pertama.example.id', $desa->domain);
    }

    public function test_domain_berubah_tidak_membuat_desa_baru()
    {
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah);

        $this->resetDomainState($wilayah->kode_desa);

        $this->postJsonWithToken('/api/track/desa', $this->payload($wilayah, 'https://domain-lama.example.id'))->assertStatus(200);

        $jumlahAwal = Desa::where('kode_desa', $wilayah->kode_desa)->count();

        $this->postJsonWithToken('/api/track/desa', $this->payload($wilayah, 'https://domain-baru.example.id'))->assertStatus(200);

        $jumlahAkhir = Desa::where('kode_desa', $wilayah->kode_desa)->count();

        $this->assertSame($jumlahAwal, $jumlahAkhir, 'Tidak boleh ada entri desa tambahan untuk kode_desa yang sama.');
        $this->assertSame(1, $jumlahAkhir);

        $desa = Desa::where('kode_desa', $wilayah->kode_desa)->first();
        $this->assertSame('domain-baru.example.id', $desa->domain);

        $histories = DomainHistory::where('desa_id', $desa->id)->orderBy('id')->get();

        $this->assertCount(2, $histories);
        $this->assertFalse($histories[0]->is_active);
        $this->assertTrue($histories[1]->is_active);
        $this->assertSame('domain-lama.example.id', $histories[0]->domain_normalized);
        $this->assertSame('domain-baru.example.id', $histories[1]->domain_normalized);
    }

    public function test_hanya_satu_domain_aktif_per_desa_setelah_beberapa_perubahan()
    {
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah);

        $this->resetDomainState($wilayah->kode_desa);

        foreach (['satu.example.id', 'dua.example.id', 'tiga.example.id'] as $domain) {
            $this->postJsonWithToken('/api/track/desa', $this->payload($wilayah, 'https://'.$domain))->assertStatus(200);
        }

        $desa = Desa::where('kode_desa', $wilayah->kode_desa)->first();

        $this->assertSame(1, DomainHistory::where('desa_id', $desa->id)->where('is_active', true)->count());
        $this->assertSame(3, DomainHistory::where('desa_id', $desa->id)->count());
        $this->assertSame('tiga.example.id', $desa->domain);
    }

    public function test_domain_yang_sama_tidak_menambah_riwayat()
    {
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah);

        $this->resetDomainState($wilayah->kode_desa);

        $this->postJsonWithToken('/api/track/desa', $this->payload($wilayah, 'https://tetap-sama.example.id'))->assertStatus(200);
        $this->postJsonWithToken('/api/track/desa', $this->payload($wilayah, 'https://www.tetap-sama.example.id'))->assertStatus(200);

        $desa = Desa::where('kode_desa', $wilayah->kode_desa)->first();

        $this->assertSame(1, DomainHistory::where('desa_id', $desa->id)->count());
    }

    public function test_domain_lokal_tidak_dicatat_sebagai_riwayat()
    {
        $wilayah = Wilayah::inRandomOrder()->first();
        $this->assertNotNull($wilayah);

        $this->resetDomainState($wilayah->kode_desa);

        $this->postJsonWithToken('/api/track/desa', $this->payload($wilayah, 'http://localhost:8000'))->assertStatus(200);

        $desa = Desa::where('kode_desa', $wilayah->kode_desa)->first();

        $this->assertSame(0, DomainHistory::where('desa_id', $desa->id)->count());
    }
}
