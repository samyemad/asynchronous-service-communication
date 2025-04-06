<?php
namespace App\InternalAuthorization\Application\CommandHandler;

use App\InternalAuthorization\Application\Command\AuthorizeDriverCommand;
use App\InternalAuthorization\Application\Security\TokenGeneratorInterface;
use App\InternalAuthorization\Application\Value\DriverTokenStatus;
use App\InternalAuthorization\Domain\Model\AuthorizationDecision;
use App\InternalAuthorization\Application\Result\AuthorizationResult;
use App\InternalAuthorization\Domain\Model\AuthorizationDecisionId;
use App\InternalAuthorization\Domain\Model\DriverId;
use App\InternalAuthorization\Domain\Model\StationId;
use App\InternalAuthorization\Application\Repository\DriverAccessRepositoryInterface;
use App\InternalAuthorization\Domain\Repository\AuthorizationDecisionRepositoryInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\InternalAuthorization\Domain\Model\AuthorizationStatus;

#[AsMessageHandler]
class AuthorizeDriverHandler
{
    public function __construct(
        private readonly TokenGeneratorInterface $tokenGenerator,
        private readonly DriverAccessRepositoryInterface $driverAccessRepository,
        private readonly AuthorizationDecisionRepositoryInterface $authorizationDecisionRepository

    ) {}

    public function __invoke(AuthorizeDriverCommand $command): AuthorizationResult
    {
        $validUntil=$this->driverAccessRepository->findValidUntilForDriver(
            $command->driverId,
            $command->stationId,
        );
        if(!$validUntil)
        {
            return new AuthorizationResult(
                $command->stationId,
                DriverTokenStatus::UNKNOWN,
                AuthorizationStatus::INVALID
            );
        }
        $authorizationDecisionId = new AuthorizationDecisionId(Uuid::uuid4()->toString());
        $stationId = new StationId($command->stationId);
        $driverId = new DriverId($command->driverId);
        $decision = new AuthorizationDecision(
            $authorizationDecisionId,
            $driverId,
            $stationId,
        );
        $decision->decide($validUntil);
        $token = DriverTokenStatus::UNKNOWN;
        if ($decision->getStatus() == 'allowed') {
            $token = $this->tokenGenerator->generate();
        }
        $this->authorizationDecisionRepository->save($decision);
        return new AuthorizationResult(
                $command->stationId,
                $token,
                $decision->getStatus(),
        );
    }
}

