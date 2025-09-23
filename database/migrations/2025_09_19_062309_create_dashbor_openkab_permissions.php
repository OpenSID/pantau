<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    private
    $permissions = [
        'laporan.openkab.view',
    ];
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole = Role::where('name', 'Administrator')->first();
        $adminWilayahRole = Role::where('name', 'Admin Wilayah')->first();


        if ($adminRole) {
            // Admin gets all permissions
            $adminRole->givePermissionTo(Permission::all());
        }

        if ($adminWilayahRole) {
            $adminWilayahRole->givePermissionTo($this->permissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::whereIn('name', $this->permissions)->each(function ($permission) {
            $permission->roles()->detach();
            $permission->users()->detach();
        });
        Permission::whereIn('name', $this->permissions)->delete();
    }
};
