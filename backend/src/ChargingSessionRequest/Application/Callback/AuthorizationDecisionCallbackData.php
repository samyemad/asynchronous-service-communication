<?php

namespace App\ChargingSessionRequest\Application\Callback;

class AuthorizationDecisionCallbackData
{
    public function __construct(
        public readonly string $stationId,
        public readonly string $driverToken,
        public readonly string $status
    )
    {
    }
}

