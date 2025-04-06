<?php
namespace App\ChargingSessionRequest\Application\Command;

class StartChargingSessionRequestCommand
{
    public function __construct(
        public readonly string $stationId,
        public readonly string $driverId,
        public readonly string $callbackUrl
    ) {
    }
}