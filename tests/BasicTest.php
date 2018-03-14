<?php

namespace Sztyup\Authsch\Tests;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Factory;
use Sztyup\Authsch\SchProvider;

class BasicTest extends TestCase
{
    public function testRedirect()
    {
        /** @var Factory $manager */
        $manager = $this->app->make(Factory::class);

        /** @var SchProvider $provider */
        $provider = $manager->driver('authsch');

        $request = \Mockery::mock(Request::class);
        $sessionStore = \Mockery::mock(Store::class);

        $request->shouldReceive('session')->andReturn($sessionStore);

        $sessionStore->shouldReceive('put');

        $provider->setRequest($request);

        $redirect = $provider->redirect();

        $this->assertInstanceOf(RedirectResponse::class, $redirect);

        $this->assertTrue(Str::startsWith($redirect->getTargetUrl(), 'https://auth.sch.bme.hu/site/login'));
    }
}