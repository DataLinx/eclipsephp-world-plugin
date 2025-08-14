<?php

namespace Eclipse\World\Policies;

use Eclipse\World\Models\Region;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;

class RegionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authorizable $user): bool
    {
        return $user->can('view_any_region');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authorizable $user, Region $region): bool
    {
        return $user->can('view_region');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Authorizable $user): bool
    {
        return $user->can('create_region');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authorizable $user, Region $region): bool
    {
        return $user->can('update_region');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authorizable $user, Region $region): bool
    {
        return $user->can('delete_region');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(Authorizable $user): bool
    {
        return $user->can('delete_any_region');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Authorizable $user, Region $region): bool
    {
        return $user->can('force_delete_region');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(Authorizable $user): bool
    {
        return $user->can('force_delete_any_region');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Authorizable $user, Region $region): bool
    {
        return $user->can('restore_region');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(Authorizable $user): bool
    {
        return $user->can('restore_any_region');
    }
}
