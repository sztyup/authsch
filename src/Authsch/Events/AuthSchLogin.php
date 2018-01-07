<?php

namespace Sztyup\Authsch\Events;

use App\Entities\SchAccount;
use Sztyup\Authsch\SchUser;

class AuthSchLogin
{
    protected $user;
    protected $shacc;

    public function __construct(SchUser $user, SchAccount $shacc)
    {
        $this->user = $user;
        $this->shacc = $shacc;
    }

    /**
     * @return SchAccount
     */
    public function getShacc(): SchAccount
    {
        return $this->shacc;
    }

    /**
     * @return SchUser
     */
    public function getUser(): SchUser
    {
        return $this->user;
    }
}
