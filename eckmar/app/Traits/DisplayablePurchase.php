<?php

namespace App\Traits;

use Cache;

/**
 * Trait Displayable.
 *
 * All methods used for displaying User or User's properties
 */
trait DisplayablePurchase
{
    /**
     * Display 5 latest COMPLETED purchases.
     */
    public static function latestOrders($count = 5)
    {
        $orders = Cache::remember('latest_orders_frontpage', config('marketplace.front_page_cache.latest_orders'), fn () => self::orderBy('created_at', 'desc')->where('state', 'delivered')->limit(5)->get());

        return $orders;
    }
}
