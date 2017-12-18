<?php

namespace Sztyup\Authsch;

class SchUser
{
    public $provider_user_id;

    public $token;
    public $refreshToken;

    public $displayName;
    public $lastName;
    public $firstName;
    public $email;

    public $schacc = null;
    public $bme_id = null;
    public $bme_status = null;

    public $dormitory = null;
    public $room_number = null;

    public $neptun = null;

    public $phone = null;

    public $circles = [];
    public $entrants = [];
    public $courses = [];

    public $admembership = [];

    const BME_STATUS_NEWBIE = 4;
    const BME_STATUS_VIK_ACTIVE = 3;
    const BME_STATUS_VIK_PASSIVE = 2;
    const BME_STATUS_BME = 1;
    const BME_STATUS_NONE = 0;

    /**
     * @param $token
     * @return SchUser
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @param $token
     * @return SchUser
     */
    public function setRefreshToken($token)
    {
        $this->refreshToken = $token;

        return $this;
    }

    /**
     * We wont store this
     *
     * @param $time
     * @return SchUser
     */
    public function setExpiresIn($time)
    {
        return $this;
    }
}