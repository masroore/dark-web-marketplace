<?php

namespace App\Providers;

use App\Packages\Captcha;

class CaptchaServiceProvider extends \Latrell\Captcha\CaptchaServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/latrell-captcha.php', 'latrell-captcha');

        $this->app->singleton('captcha', fn ($app) => Captcha::instance());
    }
}
