<?php

namespace App\Policies;

use App\Models\Tickets\Message;
use App\Role;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view tickets list.
     */
    public function index(User $user): bool
    {
        return $this->remember("policy-ticket-index-$user->id", function () use ($user) {
            return (bool) $user->roles->filter(function ($role) {
                return in_array($role->id, [
                    Role::Admin,
                    Role::SeniorModerator,
                    Role::JuniorModerator,
                    Role::SecurityService]);
            })->count();
        });
    }

    /**
     * Determine whether the user can delete the ticket.
     */
    public function destroy(User $user): bool
    {
        return $this->remember("policy-ticket-destroy-$user->id", fn () => (bool) $user->roles->filter(fn ($role) => in_array($role->id, [Role::Admin, Role::SeniorModerator]))->count());
    }

    /**
     * Determine whether the user can delete the message.
     */
    public function destroyMessage(User $user, Message $message): bool
    {
        return (bool) $user->roles->filter(function ($role) use ($user, $message) {
            return ($user->id === $message->user_id && $role->id === Role::JuniorModerator) || in_array($role->id, [
                Role::Admin, Role::SeniorModerator, Role::SecurityService]);
        })->count();
    }
}
