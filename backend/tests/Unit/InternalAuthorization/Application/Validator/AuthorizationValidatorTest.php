<?php

namespace App\Tests\Unit\InternalAuthorization\Application\Validator;

use App\InternalAuthorization\Application\Validator\AuthorizationValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AuthorizationValidatorTest extends TestCase
{
    private AuthorizationValidator $validator;

    protected function setUp(): void
    {
        $symfonyValidator = Validation::createValidator();
        $this->validator = new AuthorizationValidator($symfonyValidator);
    }

    public function test_validate_with_valid_data_passes(): void
    {
        $validData = [
            'stationId' => '123e4567-e89b-12d3-a456-426614174000',
            'driverId' => 'a1H_G6H8j1AAR9ZW.0vDJXKtTq2FyL'
        ];

        // No exception means the validation passes
        $this->expectNotToPerformAssertions();
        $this->validator->validate($validData);
    }

    public function test_validate_with_invalid_data_throws_exception(): void
    {
        $invalidData = [
            'stationId' => '', // blank and not a UUID
            'driverId' => 'inv@lid!!' // invalid characters
        ];

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Validation failed');

        $this->validator->validate($invalidData);
    }
}
