<?php

namespace Sztyup\Authsch\Events;

use Illuminate\Contracts\Auth\Authenticatable;

class AuthSchLogin
{
    protected $user;

    public function __construct(Authenticatable $user)
    {
        $this->user = $user;
    }
}