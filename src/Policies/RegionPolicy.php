<?php

declare(strict_types=1);

namespace Eclipse\World\Policies;

use Eclipse\World\Models\Region;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class RegionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(AuthUser $user): bool
    {
        return $user->can('view_any_region');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(AuthUser $user, Region $region): bool
    {
        return $user->can('view_region');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(AuthUser $user): bool
    {
        return $user->can('create_region');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(AuthUser $user, Region $region): bool
    {
        return $user->can('update_region');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(AuthUser $user, Region $region): bool
    {
        return $user->can('delete_region');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(AuthUser $user): bool
    {
        return $user->can('delete_any_region');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(AuthUser $user, Region $region): bool
    {
        return $user->can('force_delete_region');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(AuthUser $user): bool
    {
        return $user->can('force_delete_any_region');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(AuthUser $user, Region $region): bool
    {
        return $user->can('restore_region');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(AuthUser $user): bool
    {
        return $user->can('restore_any_region');
    }
}
