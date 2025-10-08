<?php

declare(strict_types=1);

namespace Workbench\App\Policies;

use Eclipse\World\Models\Country;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CountryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_country');
    }

    public function view(AuthUser $authUser, Country $country): bool
    {
        return $authUser->can('view_country');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_country');
    }

    public function update(AuthUser $authUser, Country $country): bool
    {
        return $authUser->can('update_country');
    }

    public function restore(AuthUser $authUser, Country $country): bool
    {
        return $authUser->can('restore_country');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_country');
    }

    public function replicate(AuthUser $authUser, Country $country): bool
    {
        return $authUser->can('replicate_country');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_country');
    }

    public function delete(AuthUser $authUser, Country $country): bool
    {
        return $authUser->can('delete_country');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_country');
    }

    public function forceDelete(AuthUser $authUser, Country $country): bool
    {
        return $authUser->can('force_delete_country');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_country');
    }
}
