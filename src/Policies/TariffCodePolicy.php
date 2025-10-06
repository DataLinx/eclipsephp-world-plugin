<?php

declare(strict_types=1);

namespace Eclipse\World\Policies;

use Eclipse\World\Models\TariffCode;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TariffCodePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(AuthUser $user): bool
    {
        return $user->can('view_any_tariff_code');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(AuthUser $user, TariffCode $tariffCode): bool
    {
        return $user->can('view_tariff_code');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(AuthUser $user): bool
    {
        return $user->can('create_tariff_code');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(AuthUser $user, TariffCode $tariffCode): bool
    {
        return $user->can('update_tariff_code');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(AuthUser $user, TariffCode $tariffCode): bool
    {
        return $user->can('delete_tariff_code');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(AuthUser $user): bool
    {
        return $user->can('delete_any_tariff_code');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(AuthUser $user, TariffCode $tariffCode): bool
    {
        return $user->can('restore_tariff_code');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(AuthUser $user): bool
    {
        return $user->can('restore_any_tariff_code');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(AuthUser $user, TariffCode $tariffCode): bool
    {
        return $user->can('force_delete_tariff_code');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(AuthUser $user): bool
    {
        return $user->can('force_delete_any_tariff_code');
    }
}
