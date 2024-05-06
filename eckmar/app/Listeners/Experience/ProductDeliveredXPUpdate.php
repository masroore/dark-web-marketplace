<?php

namespace App\Listeners\Experience;

use App\Events\Purchase\ProductDelivered;

class ProductDeliveredXPUpdate
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
    public function handle(ProductDelivered $event): void
    {
        $multiplier = config('experience.multipliers.product_delivered');
        $amount = round($event->purchase->getSumDollars() * $multiplier, 0);
        $event->vendor->grantExperience($amount);
    }
}
