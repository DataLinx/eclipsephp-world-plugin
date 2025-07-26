<?php

namespace Eclipse\World\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;

class SpecialRegionMembershipPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authorizable $user): bool
    {
        return $user->can('view_any_special_region_membership');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authorizable $user, $record): bool
    {
        return $user->can('view_special_region_membership');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Authorizable $user): bool
    {
        return $user->can('create_special_region_membership');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authorizable $user, $record): bool
    {
        return $user->can('update_special_region_membership');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authorizable $user, $record): bool
    {
        return $user->can('delete_special_region_membership');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(Authorizable $user): bool
    {
        return $user->can('delete_any_special_region_membership');
    }
}
