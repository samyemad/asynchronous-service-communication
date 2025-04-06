<?php
namespace App\ChargingSessionRequest\Application\CommandHandler;

use App\ChargingSessionRequest\Application\Callback\AuthorizationDecisionCallbackData;
use App\ChargingSessionRequest\Application\Command\SendAuthorizationDecisionCallbackCommand;
use App\ChargingSessionRequest\Application\Command\StartChargingSessionRequestCommand;
use App\ChargingSessionRequest\Application\Exception\AuthorizationException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class StartChargingSessionRequestHandler
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger,
        private readonly string $baseUrl
    ) {
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ExceptionInterface
     */
    public function __invoke(StartChargingSessionRequestCommand $startChargingSessionRequestCommand): void
    {
        $authorizationDecisionCallbackData = new AuthorizationDecisionCallbackData(
            $startChargingSessionRequestCommand->stationId,
            'unknown',
            'unknown'
        );

        try {
            $apiUrl = $this->baseUrl . '/api/authorize-driver';
            $response = $this->httpClient->request('POST', $apiUrl, [
                'json' => [
                    'stationId' => $startChargingSessionRequestCommand->stationId,
                    'driverId' => $startChargingSessionRequestCommand->driverId,
                ],
                'timeout' => 5.0,
            ]);
            $content = $response->toArray();
            $this->handleErrorStatus($content);
            if ($this->isValidApiResponse($content)) {
                $authorizationDecisionCallbackData = new AuthorizationDecisionCallbackData(
                    $content['station_id'],
                    $content['driver_token'],
                    $content['status']
                );
            }
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface $e) {
            $this->logger->error($e->getMessage());
        } catch (AuthorizationException $e) {
            $this->logger->error('Authorization error: ' . $e->getMessage());
        }

        $this->sendCallbackData($startChargingSessionRequestCommand->callbackUrl, $authorizationDecisionCallbackData);
    }

    /**
     * @throws AuthorizationException
     */
    private function handleErrorStatus(array $content): void
    {
        if ($content['status'] == 'error') {
            throw new AuthorizationException('Authorization failed: ' . json_encode($content));
        }
    }


    private function isValidApiResponse(array $content): bool
    {
        return isset($content['status'], $content['station_id'], $content['driver_token']);
    }

    /**
     * @throws ExceptionInterface
     */
    private function sendCallbackData(
        string $callbackUrl,
        AuthorizationDecisionCallbackData $authorizationDecisionCallbackData
    ): void
    {
        $sendToCallbackCommand = new SendAuthorizationDecisionCallbackCommand(
            $callbackUrl,
            $authorizationDecisionCallbackData
        );
        $this->messageBus->dispatch($sendToCallbackCommand);
    }
}