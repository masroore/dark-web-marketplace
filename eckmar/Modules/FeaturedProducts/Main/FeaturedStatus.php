<?php

namespace Modules\FeaturedProducts\Main;

use App\Product;
use Cache;
use Exception;

class FeaturedStatus
{
    public function getFeaturedProducts()
    {
        try {
            $cacheMinutes = (int) (config('marketplace.front_page_cache.featured_products'));
            $featuredProducts = Cache::remember('featured_products_front_page', $cacheMinutes, fn () => Product::where('featured', 1)->inRandomOrder()->limit(3)->get());
        } catch (Exception $e) {
            $featuredProducts = null;
        }

        return $featuredProducts;
    }
}
