<?php

namespace App\Providers;

use App\Packages\PriceModifier\PriceModifierService;
use Illuminate\Support\ServiceProvider;

class PriceModifierServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->singleton('price_modifier', fn () => new PriceModifierService());
    }
}
