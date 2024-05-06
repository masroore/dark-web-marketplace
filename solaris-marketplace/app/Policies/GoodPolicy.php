<?php

namespace App\Policies;

use App\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoodPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the goods list.
     */
    public function index(User $user): bool
    {
        return $this->remember("policy-good-index-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can view the good.
     */
    public function view(User $user): bool
    {
        return $this->remember("policy-good-view-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can create goods.
     */
    public function create(User $user): bool
    {
        return $this->remember("policy-good-create-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can update the good.
     */
    public function update(User $user): bool
    {
        return $this->remember("policy-good-update-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can delete the good.
     */
    public function destroy(User $user): bool
    {
        return $this->remember("policy-good-destroy-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }
}
