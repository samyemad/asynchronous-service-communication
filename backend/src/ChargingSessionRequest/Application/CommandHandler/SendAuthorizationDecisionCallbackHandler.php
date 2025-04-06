<?php

namespace App\ChargingSessionRequest\Application\CommandHandler;

use App\ChargingSessionRequest\Application\Command\SendAuthorizationDecisionCallbackCommand;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendAuthorizationDecisionCallbackHandler
{

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly HttpClientInterface $httpClient
    ) {
    }

    public function __invoke(SendAuthorizationDecisionCallbackCommand $command): void
    {
        try {
            $callbackData = $command->callbackData;

            $this->httpClient->request('POST', $command->callbackUrl, [
                'json' => [
                    'stationId' => $callbackData->stationId,
                    'driverToken' => $callbackData->driverToken,
                    'status' => $callbackData->status,
                ],
            ]);
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
