<?php

namespace App\Policies;

use App\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatsPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view shops list.
     */
    public function index(User $user): bool
    {
        return $this->remember("policy-stats-index-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin]))->count());
    }
}
