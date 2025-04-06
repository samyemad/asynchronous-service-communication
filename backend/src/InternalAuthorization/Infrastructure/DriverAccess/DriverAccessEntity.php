<?php
namespace App\InternalAuthorization\Infrastructure\DriverAccess;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: DriverAccessRepository::class)]
#[ORM\Table(name: 'driver_access')]
class DriverAccessEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $driverId;

    #[ORM\Column(type: 'string', length: 36)]
    private string $stationId;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validUntil;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive;

    public function __construct(
        string $id,
        string $driverId,
        string $stationId,
        bool $isActive,
        DateTimeImmutable $validUntil
    ) {
        $this->id = $id;
        $this->driverId = $driverId;
        $this->stationId = $stationId;
        $this->isActive = $isActive;
        $this->validUntil = $validUntil;
    }

    public function getId(): string { return $this->id; }
    public function getValidUntil(): DateTimeImmutable {
        return $this->validUntil;
    }
    public function getDriverId(): string {
        return $this->driverId;
    }

    public function getStationId(): string {
        return $this->stationId;
    }
    public function isActive(): bool {
        return $this->isActive;
    }
}
