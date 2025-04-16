<?php
namespace App\Tests\Unit\InternalAuthorization\Application\CommandHandler;

use App\InternalAuthorization\Application\CommandHandler\AuthorizeDriverHandler;
use App\InternalAuthorization\Application\Command\AuthorizeDriverCommand;
use App\InternalAuthorization\Application\Result\AuthorizationResult;
use App\InternalAuthorization\Application\Security\TokenGeneratorInterface;
use App\InternalAuthorization\Application\Value\DriverTokenStatus;
use App\InternalAuthorization\Domain\Model\AuthorizationStatus;
use App\InternalAuthorization\Application\Repository\DriverAccessRepositoryInterface;
use App\InternalAuthorization\Domain\Repository\AuthorizationDecisionRepositoryInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class AuthorizeDriverHandlerTest extends TestCase
{
    private TokenGeneratorInterface $tokenGenerator;
    private DriverAccessRepositoryInterface $driverAccessRepository;
    private AuthorizationDecisionRepositoryInterface $authorizationDecisionRepository;
    private AuthorizeDriverHandler $handler;

    private AuthorizeDriverCommand $command;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $this->driverAccessRepository = $this->createMock(DriverAccessRepositoryInterface::class);
        $this->authorizationDecisionRepository = $this->createMock(AuthorizationDecisionRepositoryInterface::class);
        $this->command = new AuthorizeDriverCommand('driver1234567890123456-._~abcd', '123e4567-e89b-12d3-a456-426614174999');
        $this->handler = new AuthorizeDriverHandler(
            $this->tokenGenerator,
            $this->driverAccessRepository,
            $this->authorizationDecisionRepository
        );
    }

    public function test_return_invalid_status_when_valid_until_not_found(): void
    {

        $this->driverAccessRepository
            ->method('findValidUntilForDriver')
            ->willReturn(null);

        $result = ($this->handler)($this->command);

        $this->assertInstanceOf(AuthorizationResult::class, $result);
        $this->assertSame(DriverTokenStatus::UNKNOWN, $result->driverToken);
        $this->assertSame(AuthorizationStatus::INVALID, $result->status);
    }

    public function test_return_allowed_status_and_generated_token_when_valid_until_in_future(): void
    {
        $validUntil = \DateTimeImmutable::createFromFormat('U', (string)(time() + 3600));

        $this->driverAccessRepository
            ->method('findValidUntilForDriver')
            ->willReturn($validUntil);

        $this->tokenGenerator
            ->method('generate')
            ->willReturn('validDriverToken123');

        $this->authorizationDecisionRepository
            ->expects($this->once())
            ->method('save');

        $result = ($this->handler)($this->command);

        $this->assertSame('validDriverToken123', $result->driverToken);
        $this->assertSame(AuthorizationStatus::ALLOWED, $result->status);
    }

    public function test_return_not_allowed_status_and_unknown_token_when_valid_until_in_past(): void
    {
        $validUntil = \DateTimeImmutable::createFromFormat('U', (string)(time() - 3600));

        $this->driverAccessRepository
            ->method('findValidUntilForDriver')
            ->willReturn($validUntil);

        $this->authorizationDecisionRepository
            ->expects($this->once())
            ->method('save');

        $result = ($this->handler)($this->command);

        $this->assertSame(DriverTokenStatus::UNKNOWN, $result->driverToken);
        $this->assertSame(AuthorizationStatus::NOT_ALLOWED, $result->status);
    }
}

