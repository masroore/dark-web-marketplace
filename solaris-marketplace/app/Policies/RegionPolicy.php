<?php

namespace App\Policies;

use App\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegionPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view regions list.
     */
    public function index(User $user): bool
    {
        return $this->remember("policy-region-index-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can view the region.
     */
    public function view(User $user): bool
    {
        return $this->remember("policy-region-view-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can create regions.
     */
    public function create(User $user): bool
    {
        return $this->remember("policy-region-create-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can update the region.
     */
    public function update(User $user): bool
    {
        return $this->remember("policy-region-update-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can delete the region.
     */
    public function destroy(User $user): bool
    {
        return $this->remember("policy-region-destroy-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }
}
