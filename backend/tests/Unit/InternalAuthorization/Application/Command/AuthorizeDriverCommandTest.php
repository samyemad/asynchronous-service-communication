<?php
namespace App\Tests\Unit\InternalAuthorization\Application\Command;

use App\InternalAuthorization\Application\Command\AuthorizeDriverCommand;
use PHPUnit\Framework\TestCase;

class AuthorizeDriverCommandTest extends TestCase
{
    public function test_authorize_driver_command_sets_properties_correctly(): void
    {
        $driverId = 'a1H_G6H8j1AAR9ZW.0vDJXKtTq2FyL';
        $stationId = '123e4567-e89b-12d3-a456-426614174000';

        $command = new AuthorizeDriverCommand($driverId, $stationId);

        $this->assertSame($driverId, $command->driverId);
        $this->assertSame($stationId, $command->stationId);
    }
}
