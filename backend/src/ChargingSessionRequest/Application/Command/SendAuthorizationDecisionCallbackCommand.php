<?php

namespace App\ChargingSessionRequest\Application\Command;

use App\ChargingSessionRequest\Application\Callback\AuthorizationDecisionCallbackData;

class SendAuthorizationDecisionCallbackCommand
{
    public function __construct(
        public readonly string                      $callbackUrl,
        public readonly AuthorizationDecisionCallbackData $callbackData
    )
    {
    }
}
