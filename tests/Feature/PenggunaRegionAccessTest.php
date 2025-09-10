<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class PenggunaRegionAccessTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    public function test_admin_can_create_user_with_region_access()
    {

        $response = $this->post(route('akun-pengguna.store'), [
            'id_grup' => 1,
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
        $this->assertDatabaseHas('user_region_access', [
            'user_id' => $user->id,
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);
    }
}
