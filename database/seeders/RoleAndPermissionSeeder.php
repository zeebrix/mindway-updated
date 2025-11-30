<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        
        $accessAdminPanelPermission = Permission::create(['name' => 'access-admin-panel']);
        $accessCounsellorPanelPermission = Permission::create(['name' => 'access-counsellor-panel']);
        $accessProgramPanelPermission = Permission::create(['name' => 'access-program-panel']);

        // Role Table Entries.
        $counsellorRole = Role::create(['name' => 'counsellor']);
        $programRole = Role::create(['name' => 'program']);
        $adminRole = Role::create(['name' => 'admin']);
        Role::create(['name' => 'customer']);
        Role::create(['name' => 'super-admin']);

        // Permission Assigment.
        $counsellorRole->givePermissionTo([
           $accessCounsellorPanelPermission
        ]);
        $programRole->givePermissionTo([
           $accessProgramPanelPermission
        ]);
        $adminRole->givePermissionTo(Permission::all());
    }
}
