<?php

namespace Sztyup\Authsch\Tests;

use Illuminate\Routing\Router;
use Laravel\Socialite\SocialiteServiceProvider;
use Orchestra\Testbench\TestCase as Base;
use Sztyup\Authsch\AuthschServiceProvider;

class TestCase extends Base
{
    protected function setUp()
    {
        $this->refreshApplication();

        /** @var Router $router */
        $router = $this->app->make('router');

        $router->get('/auth/sch/callback', function () {
            return response('callback');
        })->name('authsch.callback');
    }

    protected function getPackageProviders($app)
    {
        return [
            AuthschServiceProvider::class,
            SocialiteServiceProvider::class
        ];
    }
}
