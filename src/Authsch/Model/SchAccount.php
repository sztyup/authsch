<?php

namespace Sztyup\Authsch\Model;

use Illuminate\Database\Eloquent\Model;
use Sztyup\Authsch\Contracts\UserInterface;

class SchAccount extends Model implements UserInterface
{
    protected $table = 'sch_accounts';

    protected $fillable = [
        'user_id',
        'provider_user_id',
        'access_token',
        'refresh_token',
        'name',
        'email',
        'bme_id',
        'bme_status',
        'dormitory',
        'room_number',
        'schacc',
        'neptun',
        'phone'
    ];

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $id): UserInterface
    {
        $this->user_id = $id;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): UserInterface
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): UserInterface
    {
        $this->name = $name;

        return $this;
    }
}
