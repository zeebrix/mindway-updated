<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles & permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /**
         * DEFINE PERMISSIONS
         */
        $permissions = [
            // Admin
            'access-admin-panel',

            // Counsellor
            'access-counsellor-panel',

            // Program
            'access-program-panel',

            // Customer
            'access-customer-panel',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        /**
         * DEFINE ROLES + ASSIGN PERMISSIONS
         */
        $roles = [
            'super-admin' => $permissions, // all permissions

            'admin' => [
                'access-admin-panel',
            ],

            'counsellor' => [
                'access-counsellor-panel',
            ],

            'program' => [
                'access-program-panel',
            ],

            'customer' => [
                'access-customer-panel',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Assign permissions atomically
            $role->syncPermissions($rolePermissions);
        }
    }
}
