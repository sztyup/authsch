<?php

namespace Sztyup\Authsch;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;

class AuthschServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php',
            'authsch'
        );

        $this->loadMigrationsFrom(
            __DIR__ . '/../migrations'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('authsch.php')
        ], 'config');

        $socialite = $this->app->make(Factory::class);
        $socialite->extend(
            'authsch',
            function (Application $app) {
                return new SchProvider(
                    $app->make('request'),
                    $app->make(UrlGenerator::class),
                    $app->make('config')->get('authsch'),
                    $app->environment() !== 'production'
                );
            }
        );

        $socialite->extend(
            'authsch-bme',
            function (Application $app) {
                return new BmeProvider(
                    $app->make('request'),
                    $app->make(UrlGenerator::class),
                    $app->make('config')->get('authsch'),
                    $app->environment() !== 'production'
                );
            }
        );
    }
}
