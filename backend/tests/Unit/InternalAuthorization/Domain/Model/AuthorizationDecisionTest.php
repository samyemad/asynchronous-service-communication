<?php
namespace App\Tests\Unit\InternalAuthorization\Domain\Model;

use App\InternalAuthorization\Domain\Model\AuthorizationDecision;
use App\InternalAuthorization\Domain\Model\AuthorizationDecisionId;
use App\InternalAuthorization\Domain\Model\DriverId;
use App\InternalAuthorization\Domain\Model\StationId;
use App\InternalAuthorization\Domain\Model\AuthorizationStatus;
use PHPUnit\Framework\TestCase;

class AuthorizationDecisionTest extends TestCase
{
    private AuthorizationDecision $decision;

    protected function setUp(): void
    {
        $this->decision = new AuthorizationDecision(
            new AuthorizationDecisionId('123e4567-e89b-12d3-a456-426614174000'),
            new DriverId('Driver1234567890123456-._~abcd'),
            new StationId('123e4567-e89b-12d3-a456-426614174999')
        );
    }

    public function test_decide_sets_status_to_allowed_when_valid_until_is_in_the_future(): void
    {
        $validUntil = new \DateTimeImmutable('+1 hour');

        $this->decision->decide($validUntil);

        $this->assertSame(AuthorizationStatus::ALLOWED, $this->decision->getStatus());
    }

    public function test_decide_sets_status_to_not_allowed_when_valid_until_is_in_the_past(): void
    {
        $validUntil = new \DateTimeImmutable('-1 hour');

        $this->decision->decide($validUntil);

        $this->assertSame(AuthorizationStatus::NOT_ALLOWED, $this->decision->getStatus());
    }
}


