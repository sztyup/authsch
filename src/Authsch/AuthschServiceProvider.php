<?php

namespace Sztyup\Authsch;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AuthschServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');

        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php',
            'authsch'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('authsch.php')
        ], 'config');

        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'authsch',
            function (Container $app) {
                return new SchProvider(
                    $app->make('request'),
                    $app->make(UrlGenerator::class),
                    $app->make('config')->get('authsch')
                );
            }
        );
    }
}
