<?php
namespace App\InternalAuthorization\Application\Controller;

use App\InternalAuthorization\Application\Command\AuthorizeDriverCommand;
use App\InternalAuthorization\Application\Result\AuthorizationResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use App\InternalAuthorization\Application\Validator\AuthorizationValidator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use App\InternalAuthorization\Domain\Model\AuthorizationStatus;
use App\InternalAuthorization\Application\Value\DriverTokenStatus;

class AuthorizationController extends AbstractController
{
    #[Route('/authorize-driver', name: 'api_authorize_driver', methods: ['POST'])]
    public function authorizeDriver(
        Request $request,
        MessageBusInterface $commandBus,
        AuthorizationValidator $authorizationValidator

    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        try
        {
            $authorizationValidator->validate($data);
            $command = new AuthorizeDriverCommand(
                $data['driverId'],
                $data['stationId']

            );
            $envelope = $commandBus->dispatch($command);
            $handledStamp = $envelope->last(HandledStamp::class);
            if (!$handledStamp instanceof HandledStamp) {
                return $this->createResponse(
                    $data['stationId'],
                    DriverTokenStatus::UNKNOWN,
                    AuthorizationStatus::UNKNOWN,
                    'Error processing authorization request',
                    [],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
            $result = $handledStamp->getResult();
            if (!$result instanceof AuthorizationResult) {
                return $this->createResponse(
                    $data['stationId'],
                    DriverTokenStatus::UNKNOWN,
                    AuthorizationStatus::UNKNOWN,
                    'Invalid authorization result received',
                    [],
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
            return $this->createResponse(
                $result->stationId,
                $result->driverToken,
                $result->status,
                null,
                null,
                Response::HTTP_ACCEPTED // Return HTTP 202 Accepted
            );
        }catch (BadRequestException|ExceptionInterface|\Throwable $e) {
            return $this->handleCommandFailure($data, $e);
        }
    }
    private function handleCommandFailure($data, \Exception $e): JsonResponse
    {
        $stationId = $data['stationId'] ?? null;
        $parsedError = $this->parseExceptionMessage($e);

        return $this->createResponse(
            $stationId,
            null,
            'error',
            $parsedError['message'],
            $parsedError['errors'],
            Response::HTTP_BAD_REQUEST // Return HTTP 400 Bad Request
        );
    }
    private function parseExceptionMessage(\Exception $e): array
    {
        if ($e instanceof BadRequestException) {
            $decodedMessage = json_decode($e->getMessage(), true);

            return [
                'message' => $decodedMessage['message'] ?? $e->getMessage(),
                'errors'  => $decodedMessage['errors'] ?? [],
            ];
        }
        return [
            'message' => $e->getMessage(),
            'errors'  => [],
        ];
    }

    private function createResponse(
        ?string $stationId,
        ?string $driverToken,
        string $status,
        ?string $message = null,
        ?array $errors = [],
        int $statusCode = Response::HTTP_OK
    ): JsonResponse {
        $response = [
            'status' => $status,
            'station_id' => $stationId,
            'driver_token' => $driverToken,
        ];
        if ($message !== null) {
            $response['message'] = $message;
        }
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        return new JsonResponse($response,$statusCode);
    }
}
