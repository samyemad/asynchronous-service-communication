<?php
namespace App\Tests\Unit\InternalAuthorization\Domain\Model;

use App\InternalAuthorization\Domain\Model\StationId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class StationIdTest extends TestCase
{
    public function test_valid_station_id_is_created_successfully(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $stationId = new StationId($uuid);

        $this->assertSame($uuid, $stationId->value());
    }

    public function test_invalid_station_id_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid StationId format');

        new StationId('invalid-uuid');
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $stationId1 = new StationId($uuid);
        $stationId2 = new StationId($uuid);

        $this->assertTrue($stationId1->equals($stationId2));
    }

    public function test_equals_returns_false_for_different_values(): void
    {
        $stationId1 = new StationId('123e4567-e89b-12d3-a456-426614174000');
        $stationId2 = new StationId('123e4567-e89b-12d3-a456-426614174999');

        $this->assertFalse($stationId1->equals($stationId2));
    }
}

