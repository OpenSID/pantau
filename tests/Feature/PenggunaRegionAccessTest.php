<?php

namespace Tests\Feature;

use App\Http\Middleware\Authenticate;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PenggunaRegionAccessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_create_user_with_region_access()
    {
        $this->withoutMiddleware([Authenticate::class, \App\Http\Middleware\VerifyCsrfToken::class]);
        $response = $this->post(route('akun-pengguna.store'), [
            'role_id' => 1,
            'username' => 'testuser',
            'password' => 'Str0ngP@ssw0rd909!',
            'password_confirmation' => 'Str0ngP@ssw0rd909!',
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'provinsi_akses' => '12',
            'kabupaten_akses' => '1201',
        ]);

        $response->assertRedirect(route('akun-pengguna.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'testuser@example.com',
        ]);
        $user = User::where('username', 'testuser')->first();
        $this->assertNotNull($user);
    }
}
