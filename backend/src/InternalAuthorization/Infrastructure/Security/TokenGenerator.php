<?php
namespace App\InternalAuthorization\Infrastructure\Security;

use App\InternalAuthorization\Application\Security\TokenGeneratorInterface;
use Random\RandomException;

class TokenGenerator implements TokenGeneratorInterface
{
    /**
     * @throws RandomException
     */
    public function generate(): string
    {
        return bin2hex(random_bytes(32)); // or UUID
    }
}

