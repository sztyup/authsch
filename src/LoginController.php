<?php

namespace Sztyup\Authsch;

use Illuminate\Routing\Controller;

class LoginController extends Controller
{
    public function redirect()
    {
        return Socialite::with("authsch")->redirect();
    }

    public function callback()
    {
        $user = Socialite::with("authsch")->user();


    }
}