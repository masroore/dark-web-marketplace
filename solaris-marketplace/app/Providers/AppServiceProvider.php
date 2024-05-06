<?php

namespace App\Providers;

use App\Providers\Extensions\ValidationExtender;
use DB;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    protected $extenders = [
        ValidationExtender::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ($this->extenders as $extender) {
            /** @var Extensions\Extender $extender */
            $extender = $this->app->make($extender);
            $extender->extend();
        }

        if (config('app.debug')) {
            $kernel = $this->app->make('Illuminate\Contracts\Http\Kernel');
            $kernel->pushMiddleware('\Clockwork\Support\Laravel\ClockworkMiddleware');
            DB::enableQueryLog();
        }

        Validator::extend('pgp_public_key', fn ($attribute, $value, $parameters, $validator) => (bool) preg_match('/^-----BEGIN PGP PUBLIC KEY BLOCK-----(.*?)-----END PGP PUBLIC KEY BLOCK-----$/s', $value));
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        require __DIR__ . '/../Packages/helpers.php';
    }
}
