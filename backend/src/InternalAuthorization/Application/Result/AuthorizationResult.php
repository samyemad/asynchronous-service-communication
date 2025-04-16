<?php
namespace App\InternalAuthorization\Application\Result;

class AuthorizationResult
{
    public function __construct(
        public readonly string $stationId,
        public readonly string $driverToken,
        public readonly string $status // allowed, not_allowed, unknown, invalid
    ) {}
}

