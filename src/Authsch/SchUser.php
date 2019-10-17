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

    const BME_STATUS_BME_NEWBIE = 6;
    const BME_STATUS_VBK_NEWBIE = 5;
    const BME_STATUS_VIK_NEWBIE = 4;
    const BME_STATUS_VIK_ACTIVE = 3;
    const BME_STATUS_VIK_PASSIVE = 2;
    const BME_STATUS_BME = 1;
    const BME_STATUS_NONE = 0;

    public function __construct($id)
    {
        $this->fields = new Collection();

        $this->provider_user_id = $id;
    }

    public function getId(): string
    {
        return $this->provider_user_id;
    }

    public function setToken($token): SchUser
    {
        $this->token = $token;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setRefreshToken($token): SchUser
    {
        $this->refreshToken = $token;

        return $this;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
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

    public function setField($name, $value): SchUser
    {
        $this->fields->put($name, $value);

        return $this;
    }

    public function getField($name)
    {
        if ($name == 'provider_user_id') {
            return $this->getId();
        }

        if (!$this->fields->has($name)) {
            throw new InvalidFieldException($name);
        }

        return $this->fields->get($name);
    }

    public function hasField($name)
    {
        if ($name == 'provider_user_id') {
            return true;
        }

        return $this->fields->has($name);
    }

    public function toArray(): array
    {
        return $this->fields->toArray() + ['provider_user_id' => $this->provider_user_id];
    }
}
