<?php

namespace App\Events;

use App\GoodsPosition;
use Illuminate\Queue\SerializesModels;

class PositionDeleted
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
