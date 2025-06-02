<?php

namespace Tests\Feature\Website;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use WithoutMiddleware;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_halaman_utama()
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSeeText('Dasbor');
        $response->assertSeeText('Desa Pengguna');
        $response->assertSeeText('Kabupaten Pengguna Aktif');
        $response->assertSeeText('PantauSID');
    }
}
