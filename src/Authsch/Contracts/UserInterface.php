<?php

namespace Sztyup\Authsch\Contracts;

interface UserInterface
{
    /**
     * @return mixed
     */
    public function getUser();

    /**
     * @param $id
     *
     * @return static
     */
    public function setUser($id);

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @param string $email
     *
     * @return static
     */
    public function setEmail(string $email);

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name);
}
