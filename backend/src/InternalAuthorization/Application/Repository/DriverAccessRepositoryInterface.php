<?php
namespace App\InternalAuthorization\Application\Repository;

interface DriverAccessRepositoryInterface
{
    public function findValidUntilForDriver(string $driverId,string $stationId): ?\DateTimeImmutable;
}