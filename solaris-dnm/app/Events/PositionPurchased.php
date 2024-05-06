<?php

namespace App\Events;

use App\GoodsPosition;
use App\User;

class PositionPurchased
{
    public $position;

    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(GoodsPosition $position, User $user)
    {
        $this->position = $position;
        $this->user = $user;
    }
}
