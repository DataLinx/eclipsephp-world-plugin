<?php

declare(strict_types=1);

namespace Workbench\App\Policies;

use Eclipse\World\Models\Currency;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CurrencyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_currency');
    }

    public function view(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('view_currency');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_currency');
    }

    public function update(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('update_currency');
    }

    public function restore(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('restore_currency');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_currency');
    }

    public function replicate(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('replicate_currency');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_currency');
    }

    public function delete(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('delete_currency');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_currency');
    }

    public function forceDelete(AuthUser $authUser, Currency $currency): bool
    {
        return $authUser->can('force_delete_currency');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_currency');
    }
}
