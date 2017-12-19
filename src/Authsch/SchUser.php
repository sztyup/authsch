<?php

namespace Sztyup\Authsch;

use Illuminate\Support\Collection;
use Sztyup\Authsch\Exceptions\InvalidFieldException;

class SchUser
{
    protected $provider_user_id;

    protected $token;
    protected $refreshToken;

    protected $fields;

    const BME_STATUS_NEWBIE = 4;
    const BME_STATUS_VIK_ACTIVE = 3;
    const BME_STATUS_VIK_PASSIVE = 2;
    const BME_STATUS_BME = 1;
    const BME_STATUS_NONE = 0;

    public function __construct()
    {
        $this->fields = new Collection();
    }

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

    public function setField($name, $value)
    {
        $this->fields->put($name, $value);

        return $this;
    }

    public function getField($name)
    {
        if (!$this->fields->has($name)) {
            throw new InvalidFieldException($name);
        }

        return $this->fields->get($name);
    }
}
