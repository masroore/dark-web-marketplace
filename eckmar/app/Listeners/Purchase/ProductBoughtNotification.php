<?php

namespace App\Listeners\Purchase;

use App\Events\Purchase\NewPurchase;

class ProductBoughtNotification
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
    public function handle(NewPurchase $event): void
    {
        $content = 'Your product has been purchased by [' . $event->buyer->username . ']';
        $routeName = 'profile.sales.single';
        $routeParams = serialize(['sale' => $event->purchase->id]);
        $event->vendor->user->notify($content, $routeName, $routeParams);
    }
}
