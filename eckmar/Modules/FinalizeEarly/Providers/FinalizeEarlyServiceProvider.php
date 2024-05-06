<?php

namespace Modules\FinalizeEarly\Providers;

use Config;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;
use Modules\FinalizeEarly\Main\Info;
use Modules\FinalizeEarly\Main\Procedure;

class FinalizeEarlyServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerInfo();
        $this->registerProcedure();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('finalizeearly.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            'finalizeearly'
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/finalizeearly');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(fn ($path) => $path . '/modules/finalizeearly', Config::get('view.paths')), [$sourcePath]), 'finalizeearly');
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/finalizeearly');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'finalizeearly');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'finalizeearly');
        }
    }

    /**
     * Register an additional directory of factories.
     */
    public function registerFactories(): void
    {
        if (!app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    public function registerInfo(): void
    {
        $this->app->bind('FinalizeEarlyModule\Info', fn ($app) => new Info());
    }

    public function registerProcedure(): void
    {
        $this->app->bind('FinalizeEarlyModule\Procedure', fn ($app) => new Procedure());
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
