<?php
namespace App\InternalAuthorization\Infrastructure\Console;

use App\InternalAuthorization\Infrastructure\DriverAccess\DriverAccessEntity;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Guid\Guid;
use Random\RandomException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedDriverAccessCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct('internal-authorization:seed:driver-access');
    }

    /**
     * @throws RandomException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        // Create 5 DriverAccess records
        for ($i = 0; $i < 5; $i++) {
            $driverId = $this->generateRandomDriverId();
            $stationId = Guid::uuid4()->toString();  // Random stationId using UUID
            $validUntil = new DateTimeImmutable('+1 day');  // Valid until 1 day from now
            $driverAccessEntity = new DriverAccessEntity(
                Guid::uuid4()->toString(),  // Random ID for the DriverAccessEntity
                $driverId,
                $stationId,
                true,
                $validUntil
            );
            $this->entityManager->persist($driverAccessEntity);
        }
        $this->entityManager->flush();

        $output->writeln('5 Driver Accesses records seeded successfully.');

        return Command::SUCCESS;
    }

    /**
     * @throws RandomException
     */
    private function generateRandomDriverId(int $length = 30): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~';
        $length = max(20, min($length, 80));
        $driverId = '';
        for ($i = 0; $i < $length; $i++) {
            $driverId .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $driverId;
    }
}
