<?php

namespace App\Listeners\Experience;

use App\Events\Purchase\ProductDisputeResolved;

class ProductDisputeResolvedXPUpdate
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
    public function handle(ProductDisputeResolved $event): void
    {

        $resolvedBuyer = $event->purchase->dispute->winner->id == $event->buyer->id;

        // if its resolved in favor of buyer
        if ($resolvedBuyer) {
            $multiplier = config('experience.multipliers.product_dispute_lost');
            $amount = round($event->purchase->getSumDollars() * $multiplier, 0);
            $event->vendor->takeExperience($amount);
        }

        // if its resolved in favor of vendor
        if (!$resolvedBuyer) {
            $multiplier = config('experience.multipliers.product_delivered');
            $amount = round($event->purchase->getSumDollars() * $multiplier, 0);
            $event->vendor->grantExperience($amount);
        }
    }
}
