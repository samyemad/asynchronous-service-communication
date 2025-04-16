<?php
namespace App\Tests\Unit\InternalAuthorization\Domain\Model;

use App\InternalAuthorization\Domain\Model\DriverId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DriverIdTest extends TestCase
{
    public function test_valid_driver_id_is_created_successfully(): void
    {
        $value = 'abcDEF1234567890-._~abcd';
        $driverId = new DriverId($value);

        $this->assertSame($value, $driverId->value());
    }

    public function test_invalid_driver_id_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid DriverId format');

        // Too short and contains invalid characters
        new DriverId('invalid!');
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $value = 'ValidDriverId1234567890-._~aaaa';
        $driverId1 = new DriverId($value);
        $driverId2 = new DriverId($value);

        $this->assertTrue($driverId1->equals($driverId2));
    }

    public function test_equals_returns_false_for_different_values(): void
    {
        $driverId1 = new DriverId('ValidDriverId1234567890-._~aaaa');
        $driverId2 = new DriverId('AnotherDriverId0987654321-._~bbbb');

        $this->assertFalse($driverId1->equals($driverId2));
    }
}

