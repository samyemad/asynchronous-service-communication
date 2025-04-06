<?php
namespace App\InternalAuthorization\Infrastructure\DriverAccess;

use App\InternalAuthorization\Application\Repository\DriverAccessRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DriverAccessRepository extends ServiceEntityRepository implements DriverAccessRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        // Pass the correct entity class to the parent constructor
        parent::__construct($registry, DriverAccessEntity::class);  // Here we specify the entity class explicitly
    }

    public function findValidUntilForDriver(string $driverId,string $stationId): ?\DateTimeImmutable
    {
        $result = $this->createQueryBuilder('d')
            ->select('d.validUntil')
            ->where('d.driverId = :driverId')
            ->andWhere('d.stationId = :stationId')
            ->andWhere('d.isActive = true')
            ->setParameter('driverId', $driverId)
            ->setParameter('stationId', $stationId)
            ->getQuery()
            ->getOneOrNullResult();

        return $result ? $result['validUntil'] : null;
    }
}


