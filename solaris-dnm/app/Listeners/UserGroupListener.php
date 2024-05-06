<?php

namespace App\Listeners;

use App\Events\OrderFinished;

class UserGroupListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function orderFinished(OrderFinished $event): void
    {
        $user = $event->order->user;
        $newGroup = $user->suggestDiscountGroup();
        if ($newGroup) {
            $user->group_id = $newGroup->id;
            $user->save();
        }
    }
}
