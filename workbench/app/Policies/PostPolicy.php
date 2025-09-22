<?php

declare(strict_types=1);

namespace Workbench\App\Policies;

use Eclipse\World\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_post');
    }

    public function view(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('view_post');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_post');
    }

    public function update(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('update_post');
    }

    public function restore(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('restore_post');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_post');
    }

    public function replicate(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('replicate_post');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_post');
    }

    public function delete(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('delete_post');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any_post');
    }

    public function forceDelete(AuthUser $authUser, Post $post): bool
    {
        return $authUser->can('force_delete_post');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_post');
    }
}
