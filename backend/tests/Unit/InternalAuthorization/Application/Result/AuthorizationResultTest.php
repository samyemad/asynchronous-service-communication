<?php
namespace App\Tests\Unit\InternalAuthorization\Application\Result;

use App\InternalAuthorization\Application\Result\AuthorizationResult;
use PHPUnit\Framework\TestCase;

class AuthorizationResultTest extends TestCase
{
    public function test_authorization_result_properties_are_set_correctly(): void
    {
        $stationId = '123e4567-e89b-12d3-a456-426614174000';
        $driverToken = 'validDriverToken123';
        $status = 'allowed';

        $result = new AuthorizationResult($stationId, $driverToken, $status);

        $this->assertSame($stationId, $result->stationId);
        $this->assertSame($driverToken, $result->driverToken);
        $this->assertSame($status, $result->status);
    }
}

