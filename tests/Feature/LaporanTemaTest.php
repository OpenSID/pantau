<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Regresi issue #751: total tema bawaan harus sama dengan jumlah kartu,
 * dan Lestari hanya terdaftar sebagai Tema Pro.
 */
class LaporanTemaTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    private function buatDesaCampuran(): void
    {
        Desa::factory()->count(2)->create(['tema' => 'Esensi']);
        Desa::factory()->create(['tema' => 'Natra']);
        Desa::factory()->create(['tema' => 'Palanta']);
        Desa::factory()->create(['tema' => 'Lestari']);
        Desa::factory()->create(['tema' => 'Lestari V1.0.1']);
        Desa::factory()->create(['tema' => 'Pusako_3_8']);
    }

    public function test_total_tema_bawaan_sama_dengan_jumlah_kartu()
    {
        $totalAwal = Desa::tema();

        $this->buatDesaCampuran();

        $response = $this->actingAs($this->user)->get('/laporan/tema');

        $response->assertOk();
        $kartu = $response->viewData('listTemaCard');
        $total = $response->viewData('tema');

        $this->assertSame(array_sum(array_column($kartu, 'count')), $total);
        $this->assertSame($totalAwal + 4, $total);
    }

    public function test_lestari_tidak_tampil_sebagai_kartu_tema_bawaan()
    {
        $response = $this->actingAs($this->user)->get('/laporan/tema');

        $response->assertOk();
        $kartu = $response->viewData('listTemaCard');

        $this->assertSame(['esensi', 'natra', 'palanta'], array_keys($kartu));
        $this->assertArrayNotHasKey('lestari', $kartu);
    }

    public function test_datatable_tanpa_filter_sama_dengan_total_tema_bawaan()
    {
        $this->buatDesaCampuran();

        $response = $this->actingAs($this->user)->get('/laporan/tema', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertOk();
        $this->assertSame(Desa::tema(), $response->json('recordsTotal'));
    }

    public function test_lestari_dihitung_sebagai_tema_pro()
    {
        $daftarAwal = Desa::temaProList()->pluck('total', 'tema_nama');

        $this->buatDesaCampuran();

        $daftar = Desa::temaProList()->pluck('total', 'tema_nama');

        $this->assertSame($daftarAwal['Lestari'] + 2, $daftar['Lestari']);
    }

    public function test_tema_pro_dan_tema_bawaan_saling_eksklusif()
    {
        $pro = array_map('strtolower', Desa::TEMA_PRO);
        $bawaan = array_map('strtolower', Desa::TEMA_BAWAAN);

        $this->assertSame([], array_values(array_intersect($pro, $bawaan)));

        // Tema Pro dicocokkan dengan LIKE '%nama%', jadi nama tema bawaan
        // tidak boleh mengandung nama tema Pro (dan sebaliknya).
        foreach ($pro as $p) {
            foreach ($bawaan as $b) {
                $this->assertStringNotContainsString($p, $b, "Tema bawaan '{$b}' akan ikut terhitung sebagai Tema Pro '{$p}'.");
                $this->assertStringNotContainsString($b, $p, "Tema Pro '{$p}' mengandung nama tema bawaan '{$b}'.");
            }
        }
    }
}
