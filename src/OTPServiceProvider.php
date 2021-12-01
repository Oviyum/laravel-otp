<?php

namespace Fleetfoot\OTP;

use Illuminate\Support\ServiceProvider;

class OTPServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');

        $this->publishes([
            __DIR__ . '/config/' => config_path(),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
