<?php

namespace App\Listeners\Purchase;

use App\Events\Purchase\ProductDisputeResolved;

class ProductDisputeResolvedNotification
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
        /**
         * Notify Buyer.
         */
        $content = 'Dispute for your purchase is now resolved';
        $routeName = 'profile.purchases.single';
        $routeParams = serialize(['purchase' => $event->purchase->id]);
        $event->buyer->notify($content, $routeName, $routeParams);

        /**
         * Notify vendor.
         */
        $content = 'Dispute for your sale is now resolved';
        $routeName = 'profile.sales.single';
        $routeParams = serialize(['sale' => $event->purchase->id]);
        $event->vendor->user->notify($content, $routeName, $routeParams);
    }
}
