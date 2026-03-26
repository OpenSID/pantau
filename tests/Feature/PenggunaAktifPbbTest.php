<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pbb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;

class PenggunaAktifPbbTest extends TestCase
{
    use WithFaker;

    /**
     * Test halaman pengguna aktif PBB dapat diakses oleh user yang sudah login.
     *
     * @return void
     */
    public function test_pengguna_aktif_pbb_page_can_be_accessed_by_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/pbb/pengguna-aktif');

        $response->assertStatus(200);
        $response->assertViewIs('pbb.pengguna_aktif');
        $response->assertViewHas('fillters');
        $response->assertSee('PBB Pengguna Aktif');
        $response->assertSee('table-pbb-pengguna-aktif');
    }

    /**
     * Test halaman pengguna aktif PBB dapat diakses tanpa login.
     *
     * @return void
     */
    public function test_pengguna_aktif_pbb_page_can_be_accessed_without_authentication()
    {
        $response = $this->get('/pbb/pengguna-aktif');

        $response->assertStatus(200);
        $response->assertViewIs('pbb.pengguna_aktif');
        $response->assertSee('PBB Pengguna Aktif');
    }

    /**
     * Test AJAX request untuk data pengguna aktif PBB.
     *
     * @return void
     */
    public function test_pengguna_aktif_pbb_ajax_request_returns_json()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Buat data dummy yang aktif (updated dalam 30 hari terakhir)
        Pbb::factory()->create([
            'nama_desa' => 'Desa Aktif',
            'updated_at' => Carbon::now()->subDays(5)
        ]);

        // Buat data dummy yang tidak aktif (lebih dari 30 hari yang lalu)
        Pbb::factory()->create([
            'nama_desa' => 'Desa Tidak Aktif',
            'updated_at' => Carbon::now()->subDays(35)
        ]);

        $response = $this->get('/pbb/pengguna-aktif', [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        $json = $response->json();
        $this->assertGreaterThanOrEqual(1, count($json['data']));

        // Pastikan hanya data aktif yang muncul (tergantung implementasi controller saat ini)
        // Jika controller menggunakan where updated_at >= 30 hari
        $desaNames = collect($json['data'])->pluck('nama_desa')->toArray();
        $this->assertContains('Desa Aktif', $desaNames);
        $this->assertNotContains('Desa Tidak Aktif', $desaNames);
    }
}
