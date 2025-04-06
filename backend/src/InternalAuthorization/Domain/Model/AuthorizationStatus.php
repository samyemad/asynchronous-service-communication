<?php
namespace App\InternalAuthorization\Domain\Model;

class AuthorizationStatus
{
    public const ALLOWED = 'allowed';
    public const NOT_ALLOWED = 'not_allowed';
    public const INVALID = 'invalid';
    public const UNKNOWN = 'unknown';
}

