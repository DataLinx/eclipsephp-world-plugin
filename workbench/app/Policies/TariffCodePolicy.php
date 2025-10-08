<?php

declare(strict_types=1);

namespace Workbench\App\Policies;

use Eclipse\World\Models\TariffCode;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TariffCodePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_tariff_code');
    }

    public function view(AuthUser $authUser, TariffCode $tariffCode): bool
    {
        return $authUser->can('view_tariff_code');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_tariff_code');
    }

    public function update(AuthUser $authUser, TariffCode $tariffCode): bool
    {
        return $authUser->can('update_tariff_code');
    }

    public function restore(AuthUser $authUser, TariffCode $tariffCode): bool
    {
        return $authUser->can('restore_tariff_code');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_tariff_code');
    }

    public function replicate(AuthUser $authUser, TariffCode $tariffCode): bool
    {
        return $authUser->can('replicate_tariff_code');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_tariff_code');
    }

    public function delete(AuthUser $authUser, TariffCode $tariffCode): bool
    {
        return $authUser->can('delete_tariff_code');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_tariff_code');
    }

    public function forceDelete(AuthUser $authUser, TariffCode $tariffCode): bool
    {
        return $authUser->can('force_delete_tariff_code');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_tariff_code');
    }
}
