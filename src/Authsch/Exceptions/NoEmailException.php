<?php

namespace Sztyup\Authsch\Exceptions;

use Laravel\Socialite\Two\User;

class NoEmailException extends AuthschException
{
    public $user;

    public function __construct(User $user)
    {
        parent::__construct($user->getId());

        $this->user = $user;
    }
}
