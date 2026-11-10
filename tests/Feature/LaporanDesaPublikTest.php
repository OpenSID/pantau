<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LaporanDesaPublikTest extends TestCase
{
    use DatabaseTransactions;

    private const NAMA_PEJABAT = 'Pejabat Rahasia Test';

    private const HP_PEJABAT = '081299998888';

    protected function setUp(): void
    {
        parent::setUp();

        Desa::factory()->create([
            'nama_desa' => 'DesaKontakTest',
            'versi_lokal' => '23.01',
            'versi_hosting' => null,
            'kontak' => ['nama' => self::NAMA_PEJABAT, 'hp' => self::HP_PEJABAT],
        ]);
    }

    public function test_guest_tidak_bisa_mengakses_laporan_desa(): void
    {
        $response = $this->get('/laporan/desa?length=999', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertDontSee(self::NAMA_PEJABAT);
        $response->assertDontSee(self::HP_PEJABAT);
    }

    public function test_user_login_tetap_bisa_mengakses_laporan_desa(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/laporan/desa?length=999', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
    }

    public function test_endpoint_pengguna_opensid_untuk_guest_tidak_memuat_kontak(): void
    {
        $response = $this->get('/datatables/pengguna-opensid?length=25', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $response->assertDontSee(self::NAMA_PEJABAT);
        $response->assertDontSee(self::HP_PEJABAT);
        $response->assertDontSee('"kontak"');
    }

    public function test_endpoint_pengguna_opensid_untuk_guest_memfilter_versi_lokal(): void
    {
        Desa::factory()->create([
            'nama_desa' => 'DesaVersiBeda',
            'versi_lokal' => '22.01',
            'versi_hosting' => null,
        ]);

        $response = $this->get('/datatables/pengguna-opensid?length=25&versi_lokal=23.01', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $names = collect($response->json('data'))->pluck('nama_desa')->toArray();
        $this->assertContains('DesaKontakTest', $names);
        $this->assertNotContains('DesaVersiBeda', $names);
    }

    public function test_kolom_tanggal_publik_memakai_tgl_akses_dan_bukan_updated_at(): void
    {
        $diedit = Desa::factory()->create(['nama_desa' => 'DesaDiedit', 'versi_lokal' => '23.01', 'versi_hosting' => null]);
        $aktif = Desa::factory()->create(['nama_desa' => 'DesaAktif', 'versi_lokal' => '23.01', 'versi_hosting' => null]);

        // updated_at (waktu edit admin) dibuat paling baru, tgl_akses riil dibuat lama
        DB::table('desa')->where('id', $diedit->id)->update([
            'updated_at' => '2026-11-01 10:00:00',
            'tgl_akses_lokal' => '2024-03-10 08:00:00',
            'tgl_akses_hosting' => null,
        ]);
        // tgl_akses riil paling baru, updated_at lama
        DB::table('desa')->where('id', $aktif->id)->update([
            'updated_at' => '2020-01-01 00:00:00',
            'tgl_akses_lokal' => '2024-05-01 09:00:00',
            'tgl_akses_hosting' => '2024-04-01 09:00:00',
        ]);

        $query = http_build_query([
            'length' => 25,
            'versi_lokal' => '23.01',
            'columns' => [['data' => 'tanggal', 'name' => 'tanggal', 'orderable' => 'true', 'searchable' => 'false']],
            'order' => [['column' => 0, 'dir' => 'desc']],
        ]);

        $response = $this->get('/datatables/pengguna-opensid?'.$query, [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $data = collect($response->json('data'));
        $names = $data->pluck('nama_desa')->toArray();

        // Desa dengan tgl_akses terbaru harus di atas, meski updated_at-nya lebih lama
        $this->assertLessThan(array_search('DesaDiedit', $names), array_search('DesaAktif', $names));

        // Format tanggal sama dengan /laporan/desa (Y-m-d dari greatest tgl_akses)
        $tanggalAktif = strip_tags($data->firstWhere('nama_desa', 'DesaAktif')['tanggal']);
        $tanggalDiedit = strip_tags($data->firstWhere('nama_desa', 'DesaDiedit')['tanggal']);
        $this->assertSame('2024-05-01', trim($tanggalAktif));
        $this->assertSame('2024-03-10', trim($tanggalDiedit));
    }

    public function test_urutan_tanggal_publik_arah_asc(): void
    {
        $lama = Desa::factory()->create(['nama_desa' => 'DesaAscLama', 'versi_lokal' => '23.02', 'versi_hosting' => null]);
        $baru = Desa::factory()->create(['nama_desa' => 'DesaAscBaru', 'versi_lokal' => '23.02', 'versi_hosting' => null]);
        DB::table('desa')->where('id', $lama->id)->update(['tgl_akses_lokal' => '2023-01-01 00:00:00', 'tgl_akses_hosting' => null]);
        DB::table('desa')->where('id', $baru->id)->update(['tgl_akses_lokal' => '2024-06-01 00:00:00', 'tgl_akses_hosting' => null]);

        $names = $this->urutanTanggal('asc', '23.02');

        $this->assertLessThan(array_search('DesaAscBaru', $names), array_search('DesaAscLama', $names));
    }

    public function test_tanggal_kosong_jika_tgl_akses_lokal_dan_hosting_null(): void
    {
        $desa = Desa::factory()->create(['nama_desa' => 'DesaTanpaAkses', 'versi_lokal' => '23.03', 'versi_hosting' => null]);
        DB::table('desa')->where('id', $desa->id)->update(['tgl_akses_lokal' => null, 'tgl_akses_hosting' => null]);

        $response = $this->get('/datatables/pengguna-opensid?length=25&versi_lokal=23.03', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $item = collect($response->json('data'))->firstWhere('nama_desa', 'DesaTanpaAkses');
        $this->assertNotNull($item);
        $this->assertSame('', trim(strip_tags((string) $item['tanggal'])));
    }

    public function test_urutan_dan_format_tanggal_untuk_user_login(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $diedit = Desa::factory()->create(['nama_desa' => 'DesaLoginDiedit', 'versi_lokal' => '23.04', 'versi_hosting' => null]);
        $aktif = Desa::factory()->create(['nama_desa' => 'DesaLoginAktif', 'versi_lokal' => '23.04', 'versi_hosting' => null]);
        DB::table('desa')->where('id', $diedit->id)->update(['updated_at' => '2026-11-01 10:00:00', 'tgl_akses_lokal' => '2024-03-10 08:00:00', 'tgl_akses_hosting' => null]);
        DB::table('desa')->where('id', $aktif->id)->update(['updated_at' => '2020-01-01 00:00:00', 'tgl_akses_lokal' => '2024-05-01 09:00:00', 'tgl_akses_hosting' => '2024-04-01 09:00:00']);

        $query = http_build_query([
            'length' => 25,
            'versi_lokal' => '23.04',
            'columns' => [['data' => 'tanggal', 'name' => 'tanggal', 'orderable' => 'true', 'searchable' => 'false']],
            'order' => [['column' => 0, 'dir' => 'desc']],
        ]);

        $response = $this->actingAs($user)->get('/datatables/pengguna-opensid?'.$query, [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $data = collect($response->json('data'));
        $names = $data->pluck('nama_desa')->toArray();
        $this->assertLessThan(array_search('DesaLoginDiedit', $names), array_search('DesaLoginAktif', $names));
        $this->assertSame('2024-05-01', trim(strip_tags($data->firstWhere('nama_desa', 'DesaLoginAktif')['tanggal'])));
        $this->assertSame('2024-03-10', trim(strip_tags($data->firstWhere('nama_desa', 'DesaLoginDiedit')['tanggal'])));
    }

    /**
     * Ambil daftar nama desa terurut menurut kolom tanggal.
     *
     * @return array<int, string>
     */
    private function urutanTanggal(string $dir, string $versiLokal): array
    {
        $query = http_build_query([
            'length' => 25,
            'versi_lokal' => $versiLokal,
            'columns' => [['data' => 'tanggal', 'name' => 'tanggal', 'orderable' => 'true', 'searchable' => 'false']],
            'order' => [['column' => 0, 'dir' => $dir]],
        ]);

        $response = $this->get('/datatables/pengguna-opensid?'.$query, [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);

        return collect($response->json('data'))->pluck('nama_desa')->toArray();
    }

    public function test_halaman_detail_versi_publik_tidak_memanggil_laporan_desa(): void
    {
        $response = $this->get('/web/opensid/versi/detail');

        $response->assertStatus(200);
        $response->assertDontSee('laporan/desa');
        $response->assertSee('datatables/pengguna-opensid');
    }
}
