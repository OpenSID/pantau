<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\DomainHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DomainHistoryFilterTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Panggil endpoint datatable daftar desa.
     */
    protected function datatable(array $params = [])
    {
        $query = array_merge([
            'draw' => 1,
            'start' => 0,
            'length' => 50,
        ], $params);

        return $this->getJson('/datatables/semua-desa?'.http_build_query($query), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
    }

    public function test_daftar_desa_menampilkan_kolom_domain_dan_jumlah_riwayat()
    {
        $user = User::factory()->create();
        $desa = Desa::factory()->create([
            'kode_desa' => '11.22.33.2001',
            'domain' => 'desa-domain.example.id',
        ]);

        $response = $this->actingAs($user)->datatable([]);

        $response->assertStatus(200);
        $this->assertIsArray($response->json('data'));
        $this->assertArrayHasKey('domain', $response->json('data.0'));
        $this->assertArrayHasKey('jumlah_domain', $response->json('data.0'));
    }

    public function test_filter_domain_mencari_domain_sesuai()
    {
        $user = User::factory()->create();

        $cari = Desa::factory()->create([
            'kode_desa' => '11.22.33.2002',
            'domain' => 'cari-saya.example.id',
        ]);
        $lain = Desa::factory()->create([
            'kode_desa' => '11.22.33.2003',
            'domain' => 'lain.example.id',
        ]);

        $response = $this->actingAs($user)->datatable(['domain' => 'cari-saya']);

        $response->assertStatus(200);

        $ids = array_column($response->json('data'), 'id');

        $this->assertContains($cari->id, $ids);
        $this->assertNotContains($lain->id, $ids);
    }

    public function test_filter_kode_desa_mencari_berdasarkan_kode()
    {
        $user = User::factory()->create();

        $target = Desa::factory()->create(['kode_desa' => '11.22.33.2004']);
        $lain = Desa::factory()->create(['kode_desa' => '77.88.99.0001']);

        $response = $this->actingAs($user)->datatable(['kode_desa' => '11.22.33.2004']);

        $response->assertStatus(200);

        $ids = array_column($response->json('data'), 'id');

        $this->assertContains($target->id, $ids);
        $this->assertNotContains($lain->id, $ids);
    }

    public function test_filter_jumlah_domain_menampilkan_desa_yang_pernah_ganti_domain()
    {
        $user = User::factory()->create();

        $desa = Desa::factory()->create([
            'kode_desa' => '11.22.33.2005',
            'domain' => 'domain-baru.example.id',
        ]);

        DomainHistory::create([
            'desa_id' => $desa->id,
            'kode_desa' => $desa->kode_desa,
            'domain' => 'domain-lama.example.id',
            'domain_normalized' => 'domain-lama.example.id',
            'is_active' => false,
            'source' => 'crawler',
            'obtained_at' => now()->subMonth(),
        ]);

        DomainHistory::create([
            'desa_id' => $desa->id,
            'kode_desa' => $desa->kode_desa,
            'domain' => 'domain-baru.example.id',
            'domain_normalized' => 'domain-baru.example.id',
            'is_active' => true,
            'source' => 'crawler',
            'obtained_at' => now(),
        ]);

        $response = $this->actingAs($user)->datatable(['jumlah_domain' => '1']);

        $response->assertStatus(200);

        $ids = array_column($response->json('data'), 'id');

        $this->assertContains($desa->id, $ids);
    }

    public function test_filter_punya_riwayat_menampilkan_desa_tanpa_riwayat()
    {
        $user = User::factory()->create();

        $tanpa = Desa::factory()->create(['kode_desa' => '11.22.33.2006']);
        $tanpa->domainHistories()->delete();

        $response = $this->actingAs($user)->datatable([
            'kode_desa' => '11.22.33.2006',
            'punya_riwayat' => '0',
        ]);

        $response->assertStatus(200);

        $ids = array_column($response->json('data'), 'id');

        $this->assertContains($tanpa->id, $ids);
    }

    public function test_halaman_daftar_desa_merender_filter_domain()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/web/opensid');

        $response->assertStatus(200);
        $response->assertSee('filter-jumlah-domain', false);
        $response->assertSee('filter-punya-riwayat', false);
    }
}
