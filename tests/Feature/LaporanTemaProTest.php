<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Desa;

class LaporanTemaProTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_laporan_tema_pro_route_exists()
    {
        $response = $this->actingAs($this->user)
            ->get('/laporan/tema-pro');

        // Test that the route doesn't return 404
        $this->assertNotEquals(404, $response->status());
    }

    public function test_laporan_tema_pro_ajax_request_works()
    {
        // Create test data using theme from TEMA_PRO constant
        Desa::factory()->create([
            'tema' => Desa::TEMA_PRO[0], // Use first theme from TEMA_PRO array
        ]);

        $response = $this->actingAs($this->user)
            ->ajaxGet('/laporan/tema-pro');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data'
        ]);
    }

    public function test_laporan_tema_pro_ajax_with_tema_filter()
    {
        // Create test data using theme from TEMA_PRO constant
        $testTheme = Desa::TEMA_PRO[1]; // Use second theme from TEMA_PRO array
        Desa::factory()->create([
            'tema' => $testTheme,
        ]);

        $response = $this->actingAs($this->user)
            ->ajaxGet("/laporan/tema-pro?tema={$testTheme}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data'
        ]);
    }

    public function test_laporan_tema_pro_is_publicly_accessible()
    {
        // Test that the route is publicly accessible (no auth required)
        $response = $this->get('/laporan/tema-pro');

        // Should return 200, not redirect to login
        $this->assertNotEquals(404, $response->status());
        $this->assertNotEquals(302, $response->status()); // Not a redirect
    }

    public function test_laporan_tema_pro_controller_method_exists()
    {
        $this->assertTrue(method_exists('App\Http\Controllers\LaporanTemaProController', 'index'));
    }

    public function test_tema_pro_constant_filtering()
    {
        // Create data with themes from TEMA_PRO constant
        foreach (Desa::TEMA_PRO as $tema) {
            Desa::factory()->create(['tema' => $tema]);
        }

        // Create data with non-pro themes (should be filtered out)
        Desa::factory()->create(['tema' => 'esensi']);
        Desa::factory()->create(['tema' => 'natra']);
        Desa::factory()->create(['tema' => 'palanta']);

        $response = $this->actingAs($this->user)
            ->ajaxGet('/laporan/tema-pro');

        $response->assertStatus(200);

        $data = $response->json();

        // Should only return data with pro themes
        $this->assertEquals(count(Desa::TEMA_PRO), $data['recordsTotal']);
    }    /**
         * Helper method to make AJAX requests
         */
    protected function ajaxGet($url)
    {
        return $this->get($url, [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
    }
}
