<?php

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Workbench\App\Models\User;
use Workbench\Database\Factories\UserFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');

        // Generate Filament Shield permissions for all resources
        $this->command->info('🛡️ Generating Filament Shield permissions...');
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
        ]);
        $this->command->info('✓ Shield permissions generated');

        // Flush Spatie permission cache before assigning roles/permissions
        Artisan::call('permission:cache-reset');
        $this->command->info('✓ Spatie permission cache reset');

        // Get or create admin user
        $adminEmail = 'test@example.com';
        $adminPassword = 'password';

        $existingUser = User::where('email', $adminEmail)->first();

        if (! $existingUser) {
            $this->command->info('Creating new admin user...');
            $user = UserFactory::new()->create([
                'name' => 'Admin User',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
            ]);
            $this->command->info("Admin user created with ID: {$user->id}");
        } else {
            $this->command->info('Using existing admin user...');
            // Ensure the user has the correct password
            $existingUser->update([
                'password' => Hash::make($adminPassword),
                'email_verified_at' => now(),
            ]);
            $user = $existingUser;
            $this->command->info("Admin user found with ID: {$user->id}");
        }

        // Ensure required roles exist
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);
        $this->command->info("✓ Super admin role ensured: {$superAdminRole->name}");

        $panelUserRole = Role::firstOrCreate([
            'name' => 'panel_user',
            'guard_name' => 'web',
        ]);
        $this->command->info("✓ Panel user role ensured: {$panelUserRole->name}");

        // Attach ALL permissions to the super_admin role (single source of truth)
        $allPermissions = Permission::all();
        if ($allPermissions->count() > 0) {
            $superAdminRole->syncPermissions($allPermissions);
            $this->command->info("✓ Synced {$allPermissions->count()} permissions to role: {$superAdminRole->name}");
        } else {
            $this->command->warn('No permissions found in database to attach to super_admin role.');
        }

        // Assign only the roles we actually need to the user
        $user->syncRoles([$superAdminRole->name, $panelUserRole->name]);
        $this->command->info('✓ Assigned roles to user: '.implode(', ', $user->getRoleNames()->toArray()));

        // Refresh cache once more to reflect the latest assignments during this run
        Artisan::call('permission:cache-reset');

        if ($user) {
            $this->command->info("✓ Admin user verified: {$user->email} (ID: {$user->id})");
            $this->command->info('✓ User roles: '.$user->getRoleNames()->implode(', '));
            $this->command->info('✓ Direct permissions: '.$user->getDirectPermissions()->count().' (expected 0)');
            $this->command->info('✓ All permissions via roles: '.$user->getAllPermissions()->count());
        } else {
            $this->command->error('✗ Failed to create/update admin user!');
        }

        $this->command->info('Database seeding completed.');
        $this->command->info('');
        $this->command->info('🎯 Login Credentials:');
        $this->command->info(" Email: {$adminEmail}");
        $this->command->info(" Password: {$adminPassword}");
    }
}
