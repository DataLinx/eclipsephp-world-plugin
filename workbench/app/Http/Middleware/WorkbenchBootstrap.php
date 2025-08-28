<?php

namespace Workbench\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Workbench\App\Models\User;

class WorkbenchBootstrap
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.env') === 'local' && ! Auth::guard('web')->check()) {
            $user = User::query()->first();

            if (! $user) {
                try {
                    $user = User::query()->firstOrCreate(
                        ['email' => 'test@example.com'],
                        [
                            'name' => 'Admin User',
                            'password' => Hash::make('password'),
                            'email_verified_at' => now(),
                        ],
                    );
                } catch (\Throwable $e) {
                    Log::error('[Workbench] User creation failed', ['message' => $e->getMessage()]);
                    // In case of a race/unique constraint, fetch the existing one
                    $user = User::query()->where('email', 'test@example.com')->first();
                }
            }

            if ($user) {
                $this->bootstrapPermissionsAndAssign($user);
                Auth::guard('web')->login($user);
                $request->session()->regenerate();

                if ($request->is('admin/login')) {
                    return redirect()->to('/admin');
                }
            }
        }

        return $next($request);
    }

    private function bootstrapPermissionsAndAssign(User $user): void
    {
        // Use cache to prevent running this multiple times
        $cacheKey = 'workbench:permissions:bootstrapped';

        if (Cache::has($cacheKey)) {
            // Just ensure user has roles if already bootstrapped
            $this->ensureUserHasRoles($user);

            return;
        }

        try {
            // Use lock to prevent concurrent execution
            Cache::lock('workbench:bootstrap-permissions', 30)->block(10, function () use ($user, $cacheKey) {
                // Double-check inside the lock
                if (Cache::has($cacheKey)) {
                    return;
                }

                // Normalize guards first
                DB::table('permissions')->whereNull('guard_name')->orWhere('guard_name', '')->update(['guard_name' => 'web']);
                DB::table('roles')->whereNull('guard_name')->orWhere('guard_name', '')->update(['guard_name' => 'web']);

                // Generate Filament Shield permissions if none exist yet
                if (Permission::query()->count() === 0) {
                    Artisan::call('shield:generate', [
                        '--all' => true,
                        '--panel' => 'admin',
                    ]);
                }

                // Reset caches/registrar to ensure guards are picked up
                Artisan::call('permission:cache-reset');
                app(PermissionRegistrar::class)->forgetCachedPermissions();

                // Ensure roles with correct guard
                $this->ensureRolesHaveCorrectGuard();

                // Create roles
                $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
                $panelUser = Role::firstOrCreate(['name' => 'panel_user', 'guard_name' => 'web']);

                // Only assign permissions if the role doesn't already have them
                $this->assignPermissionsToRole($superAdmin);

                // Assign roles to user
                $this->ensureUserHasRoles($user);

                // Mark as bootstrapped (cache for 1 hour)
                Cache::put($cacheKey, true, 3600);
            });
        } catch (\Throwable $e) {
            Log::error('[Workbench] Bootstrap permissions failed', ['message' => $e->getMessage()]);
        }
    }

    private function ensureRolesHaveCorrectGuard(): void
    {
        $existingSuper = Role::where('name', 'super_admin')->first();
        if ($existingSuper && $existingSuper->guard_name !== 'web') {
            $existingSuper->guard_name = 'web';
            $existingSuper->save();
        }

        $existingPanel = Role::where('name', 'panel_user')->first();
        if ($existingPanel && $existingPanel->guard_name !== 'web') {
            $existingPanel->guard_name = 'web';
            $existingPanel->save();
        }
    }

    private function assignPermissionsToRole(Role $role): void
    {
        // Check if role already has permissions to avoid duplicate inserts
        if ($role->permissions()->count() > 0) {
            return;
        }

        $permissions = Permission::query()->where('guard_name', 'web')->get();
        if ($permissions->isNotEmpty()) {
            // Use DB transaction to ensure atomicity
            DB::transaction(function () use ($role, $permissions) {
                // Clear existing permissions first to avoid duplicates
                $role->permissions()->detach();

                // Batch insert to avoid individual constraint violations
                $pivotData = $permissions->map(function ($permission) use ($role) {
                    return [
                        'role_id' => $role->id,
                        'permission_id' => $permission->id,
                    ];
                })->toArray();

                // Use insert ignore equivalent for SQLite
                foreach ($pivotData as $data) {
                    DB::table('role_has_permissions')
                        ->insertOrIgnore($data);
                }
            });
        }
    }

    private function ensureUserHasRoles(User $user): void
    {
        $superAdmin = Role::where('name', 'super_admin')->where('guard_name', 'web')->first();
        $panelUser = Role::where('name', 'panel_user')->where('guard_name', 'web')->first();

        $rolesToAssign = collect([$superAdmin, $panelUser])
            ->filter()
            ->pluck('name')
            ->toArray();

        if (! empty($rolesToAssign)) {
            // Only sync if user doesn't already have these roles
            $existingRoles = $user->roles()->pluck('name')->toArray();
            $missingRoles = array_diff($rolesToAssign, $existingRoles);

            if (! empty($missingRoles)) {
                $user->assignRole($missingRoles);
            }
        }
    }
}
