<?php

namespace App\Providers;

use App\Packages\Referral\ReferralState;
use Illuminate\Support\ServiceProvider;

class ReferralServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {

    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->singleton('referral_state', fn ($app) => new ReferralState());
    }
}
