<?php
namespace App\Tests\Unit\ChargingSessionRequest\Application\Callback;

use PHPUnit\Framework\TestCase;
use App\ChargingSessionRequest\Application\Callback\AuthorizationDecisionCallbackData;

class AuthorizationDecisionCallbackDataTest extends TestCase
{
    public function testAuthorizationDecisionCallbackDataHoldsValues(): void
    {
        $stationId = '123e4567-e89b-12d3-a456-426614174000';
        $driverToken = 'validDriverToken123';
        $status = 'allowed';

        $data = new AuthorizationDecisionCallbackData($stationId, $driverToken, $status);

        $this->assertSame($stationId, $data->stationId);
        $this->assertSame($driverToken, $data->driverToken);
        $this->assertSame($status, $data->status);
    }
}
