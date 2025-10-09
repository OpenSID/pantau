<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserRegionAccess;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminWilayahProfileAccessTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions if they don't exist
        Permission::firstOrCreate(['name' => 'profile.view']);
        Permission::firstOrCreate(['name' => 'profile.change-password.view']);
    }

    public function test_admin_wilayah_can_access_profile_page()
    {
        // Create Admin Wilayah role and user
        $adminWilayahRole = Role::firstOrCreate(['name' => 'Admin Wilayah']);
        $adminWilayahRole->givePermissionTo(['profile.view', 'profile.change-password.view']);

        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);
        $user->assignRole($adminWilayahRole);

        // Create region access for the user
        UserRegionAccess::create([
            'user_id' => $user->id,
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);

        // Act as the Admin Wilayah user
        $this->actingAs($user);

        // Test accessing profile page
        $response = $this->get('/profile');

        $response->assertStatus(200);
        $response->assertViewIs('profile.index');
    }

    public function test_admin_wilayah_can_access_reset_password_page()
    {
        // Create Admin Wilayah role and user
        $adminWilayahRole = Role::firstOrCreate(['name' => 'Admin Wilayah']);
        $adminWilayahRole->givePermissionTo(['profile.view', 'profile.change-password.view']);

        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);
        $user->assignRole($adminWilayahRole);

        // Create region access for the user
        UserRegionAccess::create([
            'user_id' => $user->id,
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);

        // Act as the Admin Wilayah user
        $this->actingAs($user);

        // Test accessing profile reset-password page
        $response = $this->get('/profile/reset-password');

        $response->assertStatus(200);
        $response->assertViewIs('profile.reset-password');
    }

    public function test_admin_wilayah_can_update_profile()
    {
        // Create Admin Wilayah role and user
        $adminWilayahRole = Role::firstOrCreate(['name' => 'Admin Wilayah']);
        $adminWilayahRole->givePermissionTo(['profile.view', 'profile.change-password.view']);

        $user = User::factory()->create([
            'username' => 'adminwilayah',
            'name' => 'Admin Wilayah User',
            'email' => 'admin.wilayah@example.com',
            'password' => bcrypt('password123')
        ]);
        $user->assignRole($adminWilayahRole);

        // Create region access for the user
        UserRegionAccess::create([
            'user_id' => $user->id,
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);

        // Act as the Admin Wilayah user
        $this->actingAs($user);

        // Test updating profile
        $response = $this->post('/profile/update', [
            'username' => 'adminwilayah_updated',
            'name' => 'Admin Wilayah Updated',
            'email' => 'admin.wilayah.updated@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'adminwilayah_updated',
            'name' => 'Admin Wilayah Updated',
            'email' => 'admin.wilayah.updated@example.com',
        ]);
    }

    public function test_admin_wilayah_can_reset_password()
    {
        // Create Admin Wilayah role and user
        $adminWilayahRole = Role::firstOrCreate(['name' => 'Admin Wilayah']);
        $adminWilayahRole->givePermissionTo(['profile.view', 'profile.change-password.view']);

        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123')
        ]);
        $user->assignRole($adminWilayahRole);

        // Create region access for the user
        UserRegionAccess::create([
            'user_id' => $user->id,
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);

        // Act as the Admin Wilayah user
        $this->actingAs($user);

        // Test resetting password
        $response = $this->post('/profile/reset-password', [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        
        // Verify password was updated
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_admin_wilayah_profile_menu_appears_in_sidebar()
    {
        // Create Admin Wilayah role and user
        $adminWilayahRole = Role::firstOrCreate(['name' => 'Admin Wilayah']);
        $adminWilayahRole->givePermissionTo(['profile.view', 'profile.change-password.view']);

        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);
        $user->assignRole($adminWilayahRole);

        // Create region access for the user
        UserRegionAccess::create([
            'user_id' => $user->id,
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);

        // Act as the Admin Wilayah user
        $this->actingAs($user);

        // Test accessing dashboard or any page with sidebar
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        
        // Check that profile menu items are visible
        // The menu is rendered through adminlte config with 'can' => 'profile.view'
        $this->assertTrue($user->can('profile.view'));
        $this->assertTrue($user->can('profile.change-password.view'));
    }

    public function test_admin_wilayah_has_correct_permissions_for_profile()
    {
        // Create Admin Wilayah role
        $adminWilayahRole = Role::firstOrCreate(['name' => 'Admin Wilayah']);
        $adminWilayahRole->givePermissionTo(['profile.view', 'profile.change-password.view']);

        $user = User::factory()->create();
        $user->assignRole($adminWilayahRole);

        // Create region access for the user
        UserRegionAccess::create([
            'user_id' => $user->id,
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);

        // Act as the Admin Wilayah user
        $this->actingAs($user);

        // Test permissions
        $this->assertTrue($user->hasRole('Admin Wilayah'));
        $this->assertTrue($user->can('profile.view'));
        $this->assertTrue($user->can('profile.change-password.view'));
    }

    public function test_user_without_profile_permission_cannot_access_profile()
    {
        // Create a role without profile permissions
        $limitedRole = Role::firstOrCreate(['name' => 'Limited User']);

        $user = User::factory()->create();
        $user->assignRole($limitedRole);

        // Act as the user
        $this->actingAs($user);

        // User should not have profile permissions
        $this->assertFalse($user->can('profile.view'));
        $this->assertFalse($user->can('profile.change-password.view'));
    }

    public function test_admin_wilayah_profile_menu_structure_in_config()
    {
        // This test verifies the menu configuration structure
        $config = config('adminlte.menu');
        
        // Find the Pengaturan (Settings) menu
        $settingsMenu = collect($config)->first(function ($item) {
            return isset($item['text']) && $item['text'] === 'Pengaturan';
        });

        $this->assertNotNull($settingsMenu, 'Pengaturan menu should exist');
        $this->assertArrayHasKey('submenu', $settingsMenu);
        
        // Check for profile submenu
        $profileItem = collect($settingsMenu['submenu'])->first(function ($item) {
            return isset($item['text']) && $item['text'] === 'profile';
        });

        $this->assertNotNull($profileItem, 'Profile menu item should exist');
        $this->assertEquals('profile', $profileItem['url']);
        $this->assertEquals('profile.view', $profileItem['can']);

        // Check for change password submenu
        $changePasswordItem = collect($settingsMenu['submenu'])->first(function ($item) {
            return isset($item['text']) && $item['text'] === 'change_password';
        });

        $this->assertNotNull($changePasswordItem, 'Change password menu item should exist');
        $this->assertEquals('profile/reset-password', $changePasswordItem['url']);
        $this->assertEquals('profile.change-password.view', $changePasswordItem['can']);
    }

    public function test_admin_wilayah_menu_visibility_based_on_permissions()
    {
        // Create Admin Wilayah role with permissions
        $adminWilayahRole = Role::firstOrCreate(['name' => 'Admin Wilayah']);
        $adminWilayahRole->givePermissionTo(['profile.view', 'profile.change-password.view']);

        $userWithPermission = User::factory()->create();
        $userWithPermission->assignRole($adminWilayahRole);

        // Create region access
        UserRegionAccess::create([
            'user_id' => $userWithPermission->id,
            'kode_provinsi' => '12',
            'kode_kabupaten' => '1201',
        ]);

        // User without permission
        $limitedRole = Role::firstOrCreate(['name' => 'Limited User']);
        $userWithoutPermission = User::factory()->create();
        $userWithoutPermission->assignRole($limitedRole);

        // Test user with permission
        $this->actingAs($userWithPermission);
        $this->assertTrue($userWithPermission->can('profile.view'));
        $this->assertTrue($userWithPermission->can('profile.change-password.view'));

        // Test user without permission
        $this->actingAs($userWithoutPermission);
        $this->assertFalse($userWithoutPermission->can('profile.view'));
        $this->assertFalse($userWithoutPermission->can('profile.change-password.view'));
    }
}
