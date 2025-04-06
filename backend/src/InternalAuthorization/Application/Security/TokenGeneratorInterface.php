<?php
namespace App\InternalAuthorization\Application\Security;

interface TokenGeneratorInterface
{
    public function generate(): string;
}
