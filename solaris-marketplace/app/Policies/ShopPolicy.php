<?php

namespace App\Policies;

use App\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShopPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view shops list.
     */
    public function index(User $user): bool
    {
        return $this->remember("policy-shop-index-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SeniorModerator]))->count());
    }

    /**
     * Determine whether the user can view the shop.
     *
     * @return mixed
     */
    public function view(User $user): bool
    {
        return $this->remember("policy-shop-view-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SeniorModerator]))->count());
    }

    /**
     * Determine whether the user can create shops.
     *
     * @return mixed
     */
    public function create(User $user): bool
    {
        return $this->remember("policy-shop-view-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can update the shop.
     *
     * @return mixed
     */
    public function update(User $user): bool
    {
        return $this->remember("policy-shop-update-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }

    /**
     * Determine whether the user can delete the shop.
     *
     * @return mixed
     */
    public function destroy(User $user): bool
    {
        return $this->remember("policy-shop-destroy-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SecurityService]))->count());
    }
}
