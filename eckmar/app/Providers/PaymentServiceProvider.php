<?php

namespace App\Providers;

use App\Marketplace\Payment\BitcoinPayment;
use App\Marketplace\Payment\Coin;
use App\Marketplace\Payment\Escrow;
use App\Marketplace\Payment\Payment;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Return payment class
        $this->app->singleton(Payment::class, fn ($app, $parameters) => new Escrow($parameters['purchase']));
        // Return coin class
        $this->app->singleton(Coin::class, fn ($app) => new BitcoinPayment());
    }

    /**
     * Register services.
     */
    public function register(): void
    {

    }
}
