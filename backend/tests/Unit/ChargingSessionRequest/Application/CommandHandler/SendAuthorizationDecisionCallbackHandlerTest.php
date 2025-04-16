<?php

namespace App\Tests\Unit\ChargingSessionRequest\Application\CommandHandler;

use App\ChargingSessionRequest\Application\Callback\AuthorizationDecisionCallbackData;
use App\ChargingSessionRequest\Application\Command\SendAuthorizationDecisionCallbackCommand;
use App\ChargingSessionRequest\Application\CommandHandler\SendAuthorizationDecisionCallbackHandler;
use App\Tests\Unit\Fakes\FakeServerException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SendAuthorizationDecisionCallbackHandlerTest extends TestCase
{
    private HttpClientInterface $httpClientMock;
    private LoggerInterface $loggerMock;
    private SendAuthorizationDecisionCallbackHandler $handler;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClientMock = $this->createMock(HttpClientInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->handler = new SendAuthorizationDecisionCallbackHandler($this->loggerMock, $this->httpClientMock);
    }

    /**
     * @throws Exception
     */
    public function testCallbackIsSentSuccessfully(): void
    {
        $command = $this->createCommand('allowed');

        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://callback.url',
                [
                    'json' => [
                        'stationId' => '123e4567-e89b-12d3-a456-426614174000',
                        'driverToken' => 'validDriverToken123',
                        'status' => 'allowed',
                    ]
                ]
            );

        $this->loggerMock->expects($this->never())->method('error');

        $this->handler->__invoke($command);
    }

    /**
     * @throws Exception
     */
    public function testCallbackFailsAndIsLogged(): void
    {
        $command = $this->createCommand('not_allowed');

        $this->httpClientMock->expects($this->once())
            ->method('request')
            ->willThrowException(new FakeServerException());

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with('Server error');

        $this->handler->__invoke($command);
    }

    private function createCallbackData(string $status = 'allowed'): AuthorizationDecisionCallbackData
    {
        return new AuthorizationDecisionCallbackData(
            '123e4567-e89b-12d3-a456-426614174000',
            'validDriverToken123',
            $status
        );
    }

    private function createCommand(string $status = 'allowed'): SendAuthorizationDecisionCallbackCommand
    {
        return new SendAuthorizationDecisionCallbackCommand(
            'https://callback.url',
            $this->createCallbackData($status)
        );
    }
}



