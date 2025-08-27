<?php

namespace Eclipse\World\Policies;

use Eclipse\World\Models\TariffCode;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;

class TariffCodePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Authorizable $user): bool
    {
        return $user->can('view_any_tariff::code');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Authorizable $user, TariffCode $tariffCode): bool
    {
        return $user->can('view_tariff::code');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Authorizable $user): bool
    {
        return $user->can('create_tariff::code');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Authorizable $user, TariffCode $tariffCode): bool
    {
        return $user->can('update_tariff::code');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Authorizable $user, TariffCode $tariffCode): bool
    {
        return $user->can('delete_tariff::code');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(Authorizable $user): bool
    {
        return $user->can('delete_any_tariff::code');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Authorizable $user, TariffCode $tariffCode): bool
    {
        return $user->can('restore_tariff::code');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(Authorizable $user): bool
    {
        return $user->can('restore_any_tariff::code');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Authorizable $user, TariffCode $tariffCode): bool
    {
        return $user->can('force_delete_tariff::code');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(Authorizable $user): bool
    {
        return $user->can('force_delete_any_tariff::code');
    }
}
