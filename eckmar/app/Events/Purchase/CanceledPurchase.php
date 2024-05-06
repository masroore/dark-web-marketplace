<?php

namespace App\Events\Purchase;

use App\Purchase;
use Illuminate\Foundation\Events\Dispatchable;

class CanceledPurchase
{
    use Dispatchable;

    public $purchase;

    /**
     * Create a new event instance.
     */
    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }
}
