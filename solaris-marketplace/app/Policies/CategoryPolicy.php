<?php

namespace App\Policies;

use App\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view categories list.
     */
    public function index(User $user): bool
    {
        return $this->remember("policy-category-index-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can view the category.
     */
    public function view(User $user): bool
    {
        return $this->remember("policy-category-view-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $this->remember("policy-category-create-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can update the category.
     */
    public function update(User $user): bool
    {
        return $this->remember("policy-category-update-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can delete the category.
     */
    public function destroy(User $user): bool
    {
        return $this->remember("policy-category-destroy-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }
}
