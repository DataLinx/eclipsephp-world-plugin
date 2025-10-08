<?php

declare(strict_types=1);

namespace Workbench\App\Policies;

use Eclipse\World\Models\Region;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class RegionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_region');
    }

    public function view(AuthUser $authUser, Region $region): bool
    {
        return $authUser->can('view_region');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_region');
    }

    public function update(AuthUser $authUser, Region $region): bool
    {
        return $authUser->can('update_region');
    }

    public function restore(AuthUser $authUser, Region $region): bool
    {
        return $authUser->can('restore_region');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_region');
    }

    public function replicate(AuthUser $authUser, Region $region): bool
    {
        return $authUser->can('replicate_region');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_region');
    }

    public function delete(AuthUser $authUser, Region $region): bool
    {
        return $authUser->can('delete_region');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_region');
    }

    public function forceDelete(AuthUser $authUser, Region $region): bool
    {
        return $authUser->can('force_delete_region');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_region');
    }
}
