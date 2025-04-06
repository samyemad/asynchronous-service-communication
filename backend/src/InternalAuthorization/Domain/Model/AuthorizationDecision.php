<?php
namespace App\InternalAuthorization\Domain\Model;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\InternalAuthorization\Domain\Model\AuthorizationStatus;

#[ORM\Entity]
#[ORM\Table(name: 'authorization_decisions')]
class AuthorizationDecision
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;
    #[ORM\Embedded(class: DriverId::class, columnPrefix: false)]
    private DriverId $driverId;
    #[ORM\Embedded(class: StationId::class, columnPrefix: false)]
    private StationId $stationId;
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    public function __construct(
        AuthorizationDecisionId $id,
        DriverId $driverId,
        StationId $stationId,
    ) {
        $this->id = $id->value();
        $this->driverId = $driverId;
        $this->stationId = $stationId;
        $this->createdAt = new DateTimeImmutable();
        $this->status = AuthorizationStatus::INVALID;
    }

    private function isAuthorized(DateTimeImmutable $validUntil): bool
    {
        return $validUntil > new \DateTimeImmutable();
    }

    public function decide(DateTimeImmutable $validUntil): void
    {
        // Set the status based on the authorization check
        $this->status = $this->isAuthorized($validUntil) ? AuthorizationStatus::ALLOWED : AuthorizationStatus::NOT_ALLOWED;
    }

    public function getId(): AuthorizationDecisionId {
        return new AuthorizationDecisionId($this->id);
    }
    public function getDriverId(): DriverId {
        return $this->driverId;
    }
    public function getStationId(): StationId {
        return $this->stationId;
    }
    public function getCreatedAt(): DateTimeImmutable {
        return $this->createdAt;
    }
    public function getStatus(): string {
        return $this->status;
    }
}