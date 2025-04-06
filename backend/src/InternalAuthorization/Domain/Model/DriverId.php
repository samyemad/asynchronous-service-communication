<?php
namespace App\InternalAuthorization\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
class DriverId
{
    #[ORM\Column(name: "driver_id", type: "string", length: 80)]
    private string $value;

    public function __construct(string $value)
    {
        if (!preg_match('/^[A-Za-z0-9\-._~]{20,80}$/', $value)) {
            throw new InvalidArgumentException('Invalid DriverId format');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value() === $other->value();
    }
}


