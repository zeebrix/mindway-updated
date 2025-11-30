<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@mindway.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('super-admin');

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@mindway.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        $counsellor = User::create([
            'name' => 'Counsellor User',
            'email' => 'counsellor@mindway.com',
            'password' => Hash::make('password'),
            'user_type' => 'counsellor',
            'email_verified_at' => now(),
        ]);
        $counsellor->assignRole('counsellor');
        
        $programManager = User::create([
            'name' => 'Program Manager',
            'email' => 'program@mindway.com',
            'password' => Hash::make('password'),
            'user_type' => 'program',
            'email_verified_at' => now(),
        ]);
        $programManager->assignRole('program');
    }
}
