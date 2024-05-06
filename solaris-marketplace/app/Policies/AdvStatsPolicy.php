<?php

namespace App\Policies;

use App\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdvStatsPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view adv stats.
     */
    public function index(User $user): bool
    {
        return $this->remember("policy-adv-stats-index-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => $role->id == Role::Admin)->count());
    }

    public function create(User $user): bool
    {
        return $this->remember("policy-adv-stats-create-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => $role->id == Role::Admin)->count());
    }

    public function update(User $user): bool
    {
        return $this->remember("policy-adv-stats-update-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => $role->id == Role::Admin)->count());
    }

    public function destroy(User $user): bool
    {
        return $this->remember("policy-adv-stats-destroy-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => $role->id == Role::Admin)->count());
    }
}
