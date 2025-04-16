<?php
namespace App\Tests\Unit\ChargingSessionRequest\Application\Validator;

use App\ChargingSessionRequest\Application\Validator\ChargingSessionRequestValidator;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ChargingSessionRequestValidatorTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testValidData(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $chargingSessionValidator = new ChargingSessionRequestValidator($validator);

        $validData = [
            'stationId' => '123e4567-e89b-12d3-a456-426614174000',
            'driverId' => 'driver-unique-id-1234567890',
            'callbackUrl' => 'https://example.com/callback'
        ];

        $chargingSessionValidator->validate($validData);

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function testInvalidData(): void
    {
        $violation = $this->createMock(ConstraintViolation::class);
        $violation->method('getPropertyPath')->willReturn('stationId');
        $violation->method('getMessage')->willReturn('This value should not be blank.');

        $violations = new ConstraintViolationList([$violation]);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn($violations);

        $chargingSessionValidator = new ChargingSessionRequestValidator($validator);

        $invalidData = [
            'stationId' => '',
            'driverId' => 'driver',
            'callbackUrl' => 'not-a-valid-url'
        ];

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessageMatches('/Validation failed/');

        $chargingSessionValidator->validate($invalidData);
    }
}

