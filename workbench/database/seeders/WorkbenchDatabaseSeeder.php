<?php

namespace App\World\Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\World\Workbench\Models\User;

class WorkbenchDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Generate permissions using Filament Shield
        $this->command->info('Generating permissions using Filament Shield...');

        // Generate permissions for all resources
        \Artisan::call('shield:generate', [
            '--panel' => 'admin',
            '--all' => true,
        ]);

        // Create super admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);

        // Get all permissions and assign them to super admin role
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);

        // Create super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Assign super admin role to the user
        $superAdmin->assignRole($superAdminRole);

        // Also create the test user with super admin role
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $testUser->assignRole($superAdminRole);
    }
}
