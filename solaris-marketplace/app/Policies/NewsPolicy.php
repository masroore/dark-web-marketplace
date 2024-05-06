<?php

namespace App\Policies;

use App\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view news list.
     */
    public function index(User $user): bool
    {
        return $this->remember("policy-news-index-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can view the news.
     */
    public function view(User $user): bool
    {
        return $this->remember("policy-news-view-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can create news.
     */
    public function create(User $user): bool
    {
        return $this->remember("policy-news-create-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can update the news.
     */
    public function update(User $user): bool
    {
        return $this->remember("policy-news-update-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can delete the news.
     */
    public function destroy(User $user): bool
    {
        return $this->remember("policy-news-destroy-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }
}
