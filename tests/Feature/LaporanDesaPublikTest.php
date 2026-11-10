<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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

    public function test_halaman_detail_versi_publik_tidak_memanggil_laporan_desa(): void
    {
        $response = $this->get('/web/opensid/versi/detail');

        $response->assertStatus(200);
        $response->assertDontSee('laporan/desa');
        $response->assertSee('datatables/pengguna-opensid');
    }
}
