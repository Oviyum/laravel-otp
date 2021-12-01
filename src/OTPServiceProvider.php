<?php

namespace Fleetfoot\OTP;

use Illuminate\Support\ServiceProvider;
use Fleetfoot\OTP\Commands\CleanupCommand;

class OTPServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations'),
        ], 'migrations');

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
        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupCommand::class,
            ]);
        }
    }
}
