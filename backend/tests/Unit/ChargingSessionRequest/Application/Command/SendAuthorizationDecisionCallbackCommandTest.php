<?php
namespace App\Tests\Unit\ChargingSessionRequest\Application\Command;

use PHPUnit\Framework\TestCase;
use App\ChargingSessionRequest\Application\Command\SendAuthorizationDecisionCallbackCommand;
use App\ChargingSessionRequest\Application\Callback\AuthorizationDecisionCallbackData;

class SendAuthorizationDecisionCallbackCommandTest extends TestCase
{
    public function testCommandStoresCallbackUrlAndDataCorrectly(): void
    {
        $callbackUrl = 'https://example.com/callback';
        $stationId = '123e4567-e89b-12d3-a456-426614174000';
        $driverToken = 'validDriverToken123';
        $status = 'allowed';

        $callbackData = new AuthorizationDecisionCallbackData($stationId, $driverToken, $status);
        $command = new SendAuthorizationDecisionCallbackCommand($callbackUrl, $callbackData);

        $this->assertSame($callbackUrl, $command->callbackUrl);
        $this->assertSame($callbackData, $command->callbackData);
        $this->assertSame($stationId, $command->callbackData->stationId);
        $this->assertSame($driverToken, $command->callbackData->driverToken);
        $this->assertSame($status, $command->callbackData->status);
    }
}
