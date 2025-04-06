<?php
namespace App\InternalAuthorization\Application\Command;

class AuthorizeDriverCommand
{
    public function __construct(
        public readonly string $driverId,
        public readonly string $stationId,
    ) {}


}

