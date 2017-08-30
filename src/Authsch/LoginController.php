<?php

namespace Sztyup\Authsch;

use Illuminate\Routing\Controller;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function redirect()
    {
        return Socialite::with("authsch")->redirect();
    }

    public function callback()
    {
        $provided_user = Socialite::with("authsch")->user();

        $config = config("authsch");

        $userClass = $config["user"]["class"];

        $user = $userClass::where("email", $provided_user->email)->get();
        if($user == null) { //No user with that email exists in the DB, create one
            $user = new $userClass;

            $fields = $config["user"]["fields"];
            foreach($fields as $field => $provided_field) {
                $user->$field = $provided_user->$provided_field;
            }

            $user->save();
        }
        else if($config["update_when_login"]) {
            $fields = $config["user"]["fields"];
            foreach($fields as $field => $provided_field) {
                $user->$field = $provided_user->$provided_field;
            }
            $user->save();
        }

        \Auth::login($user);

        return redirect()->route($config["redirect_route"]);
    }
}