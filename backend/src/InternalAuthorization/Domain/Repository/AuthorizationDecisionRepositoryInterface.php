<?php
namespace App\InternalAuthorization\Domain\Repository;

use App\InternalAuthorization\Domain\Model\AuthorizationDecision;

interface AuthorizationDecisionRepositoryInterface
{
    public function save(AuthorizationDecision $authorizationDecision): void;
}