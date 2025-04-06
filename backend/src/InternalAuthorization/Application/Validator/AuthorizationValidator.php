<?php
namespace App\InternalAuthorization\Application\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AuthorizationValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(array $data): void
    {
        $constraint = new Assert\Collection([
            'stationId' => [
                new Assert\NotBlank(),
                new Assert\Uuid(),
            ],
            'driverId' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 20, 'max' => 80]),
                new Assert\Regex([
                    'pattern' => '/^[A-Za-z0-9\-._~]+$/',
                    'message' => 'Driver ID contains invalid characters.',
                ])
            ]
        ]);

        $violations = $this->validator->validate($data, $constraint);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            throw new BadRequestException(json_encode([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $errors
            ]));
        }
    }
}

