<?php
namespace App\InternalAuthorization\Domain\Model;

use InvalidArgumentException;


class AuthorizationDecisionId
{
    private string $id;

    public function __construct(string $value)
    {
        if (!$this->isValid($value)) {
            throw new InvalidArgumentException('Invalid AuthorizationDecisionId format');
        }
        $this->id = $value;
    }

    private function isValid(string $value): bool
    {
        return preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/', $value);
    }

    public function value(): string
    {
        return $this->id;
    }

    public function equals(self $other): bool
    {
        return $this->id === $other->id;
    }
}


