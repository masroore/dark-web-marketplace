<?php

namespace App\Events;

use App\Order;
use Event;
use Illuminate\Queue\SerializesModels;

class OrderFinished extends Event
{
    use SerializesModels;

    /**
     * @var Order
     */
    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
