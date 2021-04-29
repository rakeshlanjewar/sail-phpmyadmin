<?php

namespace Rakeshlanjewar\SailPhpmyadmin;

use Illuminate\Support\ServiceProvider;
use Rakeshlanjewar\SailPhpmyadmin\Commands\SailphpMyAdmin;

class SailPhpmyadminServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->commands([
                SailphpMyAdmin::class
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sail-phpmyadmin'];
    }
}
