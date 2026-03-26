<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LaporanDesaTest extends TestCase
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

    public function test_tipe_pengguna_premium_filter()
    {
        $desaPremium = Desa::factory()->create([
            'nama_desa' => 'DesaPremiumTest',
            'versi_hosting' => '23.01-premium',
            'tema' => 'Lestari',
        ]);
        
        $desaUmum = Desa::factory()->create([
            'nama_desa' => 'DesaUmumTest',
            'versi_hosting' => '23.01',
            'tema' => 'Natra',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/laporan/desa?tipe_pengguna=premium&length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $names = collect($data)->pluck('nama_desa')->toArray();
        $this->assertContains($desaPremium->nama_desa, $names);
        $this->assertNotContains($desaUmum->nama_desa, $names);
    }

    public function test_tipe_pengguna_umum_filter()
    {
        $desaPremium = Desa::factory()->create([
            'nama_desa' => 'DesaPremiumTest',
            'versi_hosting' => '23.01-premium',
        ]);
        
        $desaUmum = Desa::factory()->create([
            'nama_desa' => 'DesaUmumTest',
            'versi_hosting' => '23.01',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/laporan/desa?tipe_pengguna=umum&length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $names = collect($data)->pluck('nama_desa')->toArray();
        $this->assertContains($desaUmum->nama_desa, $names);
        $this->assertNotContains($desaPremium->nama_desa, $names);
    }

    public function test_tema_column_included_in_response()
    {
        $desa = Desa::factory()->create([
            'nama_desa' => 'DesaTemaTest',
            'tema' => 'Pusako',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/laporan/desa?length=999', [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $desaData = collect($data)->firstWhere('nama_desa', $desa->nama_desa);
        $this->assertNotNull($desaData);
        $this->assertEquals('Pusako', $desaData['tema']);
    }
}
