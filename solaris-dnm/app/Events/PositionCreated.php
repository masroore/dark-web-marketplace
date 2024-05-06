<?php

namespace App\Events;

use App\GoodsPosition;
use Illuminate\Queue\SerializesModels;

class PositionCreated
{
    use SerializesModels;

    public $position;

    /**
     * Create a new event instance.
     */
    public function __construct(GoodsPosition $position)
    {
        $this->position = $position;
    }
}
