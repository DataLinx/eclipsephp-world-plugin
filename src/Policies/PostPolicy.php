<?php

declare(strict_types=1);

namespace Eclipse\World\Policies;

use Eclipse\World\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(AuthUser $user): bool
    {
        return $user->can('view_any_post');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(AuthUser $user): bool
    {
        return $user->can('create_post');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(AuthUser $user, Post $post): bool
    {
        return $user->can('update_post');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(AuthUser $user, Post $post): bool
    {
        return $user->can('delete_post');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(AuthUser $user): bool
    {
        return $user->can('delete_any_post');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(AuthUser $user, Post $post): bool
    {
        return $user->can('force_delete_post');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(AuthUser $user): bool
    {
        return $user->can('force_delete_any_post');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(AuthUser $user, Post $post): bool
    {
        return $user->can('restore_post');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(AuthUser $user): bool
    {
        return $user->can('restore_any_post');
    }
}
