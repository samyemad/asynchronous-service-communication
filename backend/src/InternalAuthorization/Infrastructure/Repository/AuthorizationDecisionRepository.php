<?php
namespace App\InternalAuthorization\Infrastructure\Repository;


use App\InternalAuthorization\Domain\Model\AuthorizationDecision;
use App\InternalAuthorization\Domain\Repository\AuthorizationDecisionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class AuthorizationDecisionRepository implements AuthorizationDecisionRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function save(AuthorizationDecision $authorizationDecision): void
    {
        $this->entityManager->persist($authorizationDecision);
        $this->entityManager->flush();
    }
}
