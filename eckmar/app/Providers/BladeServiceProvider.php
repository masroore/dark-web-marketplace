<?php

namespace App\Providers;

use App\Marketplace\ModuleManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Route;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap Blade Engine services.
     */
    public function boot(): void
    {
        Blade::if('error', fn ($name, $errors) => $errors->has($name));

        // if we are on  this route custom blade directive
        Blade::if('isroute', fn ($routeName) => str_contains(Route::currentRouteName(), $routeName));

        Blade::if('vendor', fn () => auth()->check() && auth()->user()->isVendor());
        // check if the logged user is admin
        Blade::if('admin', fn () => auth()->check() && auth()->user()->isAdmin());

        Blade::if('moderator', fn () => auth()->check() && auth()->user()->hasPermissions() && !auth()->user()->isAdmin());

        Blade::if('hasAccess', fn ($permission) => auth()->check() && (auth()->user()->isAdmin() || auth()->user()->hasPermission($permission)));

        Blade::if('isModuleEnabled', fn ($moduleName) => ModuleManager::isEnabled($moduleName));

        Blade::if('search', function () {

            $display = false;
            $routes = [
                'home',
                'category',
            ];
            foreach ($routes as $route) {

                if (str_contains(Route::currentRouteName(), $route)) {

                    $display = true;

                    break;
                }
            }

            return $display;

        });

    }

    /**
     * Register services.
     */
    public function register(): void
    {

    }
}
