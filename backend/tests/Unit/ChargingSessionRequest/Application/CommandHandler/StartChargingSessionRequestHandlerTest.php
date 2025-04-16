<?php

namespace App\Tests\Unit\ChargingSessionRequest\Application\CommandHandler;

use App\ChargingSessionRequest\Application\Callback\AuthorizationDecisionCallbackData;
use App\ChargingSessionRequest\Application\Command\SendAuthorizationDecisionCallbackCommand;
use App\ChargingSessionRequest\Application\Command\StartChargingSessionRequestCommand;
use App\ChargingSessionRequest\Application\CommandHandler\StartChargingSessionRequestHandler;
use App\ChargingSessionRequest\Application\Exception\AuthorizationException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class StartChargingSessionRequestHandlerTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private MessageBusInterface $messageBus;
    private LoggerInterface $logger;
    private StartChargingSessionRequestHandler $handler;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->handler = new StartChargingSessionRequestHandler(
            $this->httpClient,
            $this->messageBus,
            $this->logger,
            'http://localhost'
        );
    }

    /**
     * @throws Exception
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ExceptionInterface
     */
    public function testValidApiResponse(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn([
            'station_id' => '123e4567-e89b-12d3-a456-426614174000',
            'driver_token' => 'validDriverToken123',
            'status' => 'allowed'
        ]);

        $this->httpClient->method('request')->willReturn($response);

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($command) {
                return $command instanceof SendAuthorizationDecisionCallbackCommand
                    && $command->callbackUrl === 'https://callback.url'
                    && $command->callbackData->stationId === '123e4567-e89b-12d3-a456-426614174000'
                    && $command->callbackData->driverToken === 'validDriverToken123'
                    && $command->callbackData->status === 'allowed';
            }))
            ->willReturn(new Envelope(new \stdClass()));

        ($this->handler)($this->createValidCommand());
    }

    /**
     * @throws Exception
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ExceptionInterface
     */
    public function testAuthorizationException(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('toArray')->willReturn(['status' => 'error']);

        $this->httpClient->method('request')->willReturn($response);

        $this->logger->expects($this->once())->method('error')->with($this->stringContains('Authorization failed'));

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(SendAuthorizationDecisionCallbackCommand::class))
            ->willReturn(new Envelope(new \stdClass()));

        ($this->handler)($this->createValidCommand());
    }

    /**
     * @throws Exception
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ExceptionInterface
     */
    public function testHttpClientTransportException(): void
    {
        $this->httpClient->method('request')->willThrowException(
            $this->createMock(TransportExceptionInterface::class)
        );

        $this->logger->expects($this->once())->method('error');

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(SendAuthorizationDecisionCallbackCommand::class))
            ->willReturn(new Envelope(new \stdClass()));

        ($this->handler)($this->createValidCommand());
    }

    private function createValidCommand(): StartChargingSessionRequestCommand
    {
        return new StartChargingSessionRequestCommand(
            '123e4567-e89b-12d3-a456-426614174000',
            'a1H_G6H8j1AAR9ZW.0vDJXKtTq2FyL',
            'https://callback.url'
        );
    }
}

