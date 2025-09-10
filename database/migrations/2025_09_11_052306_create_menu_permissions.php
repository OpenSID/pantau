<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions based on menu structure
        $permissions = [
            // Dashboard & Main Menu
            'dashboard.view',
            'web.view',
            'peta.view',

            // Laporan
            'laporan.view',
            'laporan.desa.view',
            'laporan.kabupaten.view',
            'laporan.versi.view',
            'laporan.desa-aktif.view',
            'laporan.tema.view',

            // OpenDK
            'opendk.view',
            'opendk.kecamatan.view',
            'opendk.kabupaten.view',
            'opendk.versi.view',

            // LayananDesa
            'mobile.view',
            'mobile.desa.view',
            'mobile.pengguna.view',
            'mobile.pengguna_kelola_desa.view',

            // OpenKab
            'openkab.view',
            'openkab.kerja-sama.view',

            // PBB
            'pbb.view',
            'pbb.kecamatan.view',
            'pbb.kabupaten.view',
            'pbb.versi.view',

            // Master Data
            'wilayah.view',
            'suku.view',
            'marga.view',
            'adat.view',
            'pekerjaan-pmi.view',

            // Review (Admin only)
            'review.view',
            'review.non-aktif.view',
            'review.desa-baru.view',

            // Maintenance (Admin only)
            'akses.bersihkan.view',

            // Data Wilayah (Admin only)
            'data-wilayah.view',
            'provinsi.view',
            'kabupaten.view',
            'kecamatan.view',
            'desa.view',

            // User Management (Admin only)
            'pengguna.view',
            'pengguna.create',
            'pengguna.edit',
            'pengguna.delete',

            // Settings (Admin only)
            'pengaturan.view',
            'pengaturan.aplikasi.view',
            'profile.view',
            'profile.change-password.view',
        ];

        foreach ($permissions as $permission) {
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
            // Admin Wilayah gets limited permissions (mainly viewing and region-specific data)
            $adminWilayahPermissions = [
                'dashboard.view',
                'web.view',
                'peta.view',
                'laporan.view',
                'laporan.desa.view',
                'laporan.kabupaten.view',
                'laporan.versi.view',
                'laporan.desa-aktif.view',
                'laporan.tema.view',
                'opendk.view',
                'opendk.kecamatan.view',
                'opendk.kabupaten.view',
                'opendk.versi.view',
                'mobile.view',
                'mobile.desa.view',
                'wilayah.view',
                'suku.view',
                'marga.view',
                'adat.view',
                'pekerjaan-pmi.view',
                'profile.view',
                'profile.change-password.view',
            ];

            $adminWilayahRole->givePermissionTo($adminWilayahPermissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Remove all permissions created by this migration
        $permissions = [
            'dashboard.view', 'web.view', 'peta.view',
            'laporan.view', 'laporan.desa.view', 'laporan.kabupaten.view', 'laporan.versi.view', 'laporan.desa-aktif.view', 'laporan.tema.view',
            'opendk.view', 'opendk.kecamatan.view', 'opendk.kabupaten.view', 'opendk.versi.view',
            'mobile.view', 'mobile.desa.view', 'mobile.pengguna.view', 'mobile.pengguna_kelola_desa.view',
            'openkab.view', 'openkab.kerja-sama.view',
            'pbb.view', 'pbb.kecamatan.view', 'pbb.kabupaten.view', 'pbb.versi.view',
            'wilayah.view', 'suku.view', 'marga.view', 'adat.view', 'pekerjaan-pmi.view',
            'review.view', 'review.non-aktif.view', 'review.desa-baru.view',
            'akses.bersihkan.view',
            'data-wilayah.view', 'provinsi.view', 'kabupaten.view', 'kecamatan.view', 'desa.view',
            'pengguna.view', 'pengguna.create', 'pengguna.edit', 'pengguna.delete',
            'pengaturan.view', 'pengaturan.aplikasi.view', 'profile.view', 'profile.change-password.view',
        ];
        Permission::whereIn('name', $permissions)->each(function ($permission) {
            $permission->roles()->detach();
            $permission->users()->detach();
        });
        Permission::whereIn('name', $permissions)->delete();
    }
};
