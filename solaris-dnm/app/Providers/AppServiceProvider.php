<?php

namespace App\Providers;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.debug')) {
            $kernel = $this->app->make('Illuminate\Contracts\Http\Kernel');
            $kernel->pushMiddleware('\Clockwork\Support\Laravel\ClockworkMiddleware');

            if (config('mm2.debug.enable_sql_query_log')) {
                DB::enableQueryLog();
            }
        }

        Validator::extend('not_in_icase', function ($attribute, $value, $parameters, $validator) {
            return !collect($parameters)
                ->map(fn ($value) => mb_strtolower($value))
                ->contains(mb_strtolower($value));
        });

        Validator::extend('not_starts_with_letter', fn ($attribute, $value, $parameters, $validator) => !starts_with_letter($value, collect($parameters)));

        Validator::extend('not_starts_with_word', function ($attribute, $value, $parameters, $validator) {
            $parameters = collect($parameters);
            foreach ($parameters as $word) {
                if (starts_with_word($value, $word)) {
                    return false;
                }
            }

            return true;
        });

        Validator::extend('pgp_public_key', fn ($attribute, $value, $parameters, $validator) => (bool) preg_match('/^-----BEGIN PGP PUBLIC KEY BLOCK-----(.*?)-----END PGP PUBLIC KEY BLOCK-----$/s', $value));

        AbstractPaginator::currentPathResolver(function () {
            /** @var \Illuminate\Routing\UrlGenerator $url */
            $url = app('url');

            return $url->current();
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        require __DIR__ . '/../Packages/helpers.php';
    }
}
