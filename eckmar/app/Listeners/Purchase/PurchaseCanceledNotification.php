<?php

namespace App\Listeners\Purchase;

use App\Events\Purchase\CanceledPurchase;

class PurchaseCanceledNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     */
    public function handle(CanceledPurchase $event): void
    {
        // Notify vendor
        $content = 'Your sale has been canceled.';
        $routeName = 'profile.sales.single';
        $routeParams = serialize(['sale' => $event->purchase->id]);
        $event->purchase->vendor->user->notify($content, $routeName, $routeParams);

        // Notifiy buyer
        $content = 'Your purchase has been canceled.';
        $routeName = 'profile.purchases.single';
        $routeParams = serialize(['purchase' => $event->purchase->id]);
        $event->purchase->buyer->notify($content, $routeName, $routeParams);
    }
}
