<?php
namespace App\Tests\Unit\ChargingSessionRequest\Application\Command;

use PHPUnit\Framework\TestCase;
use App\ChargingSessionRequest\Application\Command\StartChargingSessionRequestCommand;

class StartChargingSessionRequestCommandTest extends TestCase
{
    public function testCommandStoresDataCorrectly(): void
    {
        $stationId = '123e4567-e89b-12d3-a456-426614174000';
        $driverId = 'a1H_G6H8j1AAR9ZW.0vDJXKtTq2FyL';
        $callbackUrl = 'https://example.com/callback';

        $command = new StartChargingSessionRequestCommand($stationId, $driverId, $callbackUrl);

        $this->assertSame($stationId, $command->stationId);
        $this->assertSame($driverId, $command->driverId);
        $this->assertSame($callbackUrl, $command->callbackUrl);
    }
}

