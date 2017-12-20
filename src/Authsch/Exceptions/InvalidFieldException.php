<?php

namespace Sztyup\Authsch\Exceptions;

class InvalidFieldException extends \Exception
{
    public function __construct($field)
    {
        parent::__construct("Invalid field [$field] requested, check your scopes", 0, null);
    }
}
