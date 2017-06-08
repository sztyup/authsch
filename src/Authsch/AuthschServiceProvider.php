<?php

namespace Sztyup\Authsch;

use Illuminate\Support\ServiceProvider;

class AuthschServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->make('Sztyup\Authsch\LoginController.php');

        $this->loadRoutesFrom(__DIR__ . "routes/routes.php");

        $this->mergeConfigFrom(
            __DIR__.'/config/config.php', 'authsch'
        );
    }
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/config.php' => config_path('authsch.php')
        ]);
    }
}