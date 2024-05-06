<?php

namespace App\Policies;

use App\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CityPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view cities list.
     */
    public function index(User $user): bool
    {
        return $this->remember("policy-city-index-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can view the city.
     */
    public function view(User $user): bool
    {
        return $this->remember("policy-city-view-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can create cities.
     */
    public function create(User $user): bool
    {
        return $this->remember("policy-city-create-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can update the city.
     */
    public function update(User $user): bool
    {
        return $this->remember("policy-city-update-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can delete the city.
     */
    public function destroy(User $user): bool
    {
        return $this->remember("policy-city-destroy-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }
}
