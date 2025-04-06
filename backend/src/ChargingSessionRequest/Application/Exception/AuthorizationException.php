<?php

namespace App\ChargingSessionRequest\Application\Exception;

use Exception;

class AuthorizationException extends Exception
{
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }
}

