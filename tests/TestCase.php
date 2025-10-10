<?php

namespace Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Workbench\App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use WithWorkbench;

    protected ?User $superAdmin = null;

    protected ?User $user = null;

    protected function setUp(): void
    {
        // Always show errors when testing
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        parent::setUp();

        $this->withoutVite();
    }

    /**
     * Run database migrations
     */
    protected function migrate(): self
    {
        $this->artisan('migrate');

        return $this;
    }

    /**
     * Set up default "super admin" user
     */
    protected function setUpSuperAdmin(): self
    {
        $this->superAdmin = User::factory()->create();

        // Assign super admin role and give all permissions
        $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $this->superAdmin->assignRole($superAdminRole);
            // Give all permissions to super admin role
            $permissions = \Spatie\Permission\Models\Permission::all();
            $superAdminRole->syncPermissions($permissions);
        }

        $this->actingAs($this->superAdmin);

        return $this;
    }

    /**
     * Set up a common user with no roles or permissions
     */
    protected function setUpCommonUser(): self
    {
        $this->user = User::factory()->create();

        $this->actingAs($this->user);

        return $this;
    }

    public function ignorePackageDiscoveriesFrom()
    {
        return [
            // A list of packages that should not be auto-discovered when running tests
            'laravel/telescope',
        ];
    }

    /**
     * Create permissions for all resources.
     */
    protected function createPermissions(): self
    {
        $resources = [
            'country',
            'currency',
            'post',
            'region',
            'tariff_code',
        ];

        $permissions = [
            'view_any',
            'view',
            'create',
            'update',
            'restore',
            'restore_any',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ];

        foreach ($resources as $resource) {
            foreach ($permissions as $permission) {
                \Spatie\Permission\Models\Permission::create([
                    'name' => $permission.'_'.$resource,
                    'guard_name' => 'web',
                ]);
            }
        }

        return $this;
    }

    /**
     * Create roles
     */
    protected function createRoles(): self
    {
        \Spatie\Permission\Models\Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        \Spatie\Permission\Models\Role::create([
            'name' => 'panel_user',
            'guard_name' => 'web',
        ]);

        return $this;
    }
}
