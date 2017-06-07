<?php

namespace Sztyup\Authsch;

use Illuminate\Support\ServiceProvider;

class AuthschServiceProvider extends ServiceProvider
{
    public function register()
    {
        include __DIR__.'/routes.php';
        $this->app->make('Sztyup\Authsch\LoginController.php');
    }
    public function boot()
    {

    }
}