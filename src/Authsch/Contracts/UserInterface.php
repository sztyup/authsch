<?php

namespace Sztyup\Authsch\Contracts;

interface UserInterface
{
    public function getUserId(): int;
    public function setUserId(int $id): UserInterface;

    public function getEmail(): string;
    public function setEmail(string $email): UserInterface;

    public function getName(): string;
    public function setName(string $name): UserInterface;
}
