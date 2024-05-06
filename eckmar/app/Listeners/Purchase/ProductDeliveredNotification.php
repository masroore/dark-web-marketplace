<?php

namespace App\Listeners\Purchase;

use App\Events\Purchase\ProductDelivered;

class ProductDeliveredNotification
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
        $content = 'Your product has been marked delivered by buyer [' . $event->buyer->username . ']';
        $routeName = 'profile.sales.single';
        $routeParams = serialize(['sales' => $event->purchase->id]);
        $event->vendor->user->notify($content, $routeName, $routeParams);
    }
}
