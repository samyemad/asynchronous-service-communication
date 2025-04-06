<?php
namespace App\ChargingSessionRequest\Application\Controller;

use App\ChargingSessionRequest\Application\Command\StartChargingSessionRequestCommand;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\ChargingSessionRequest\Application\Validator\ChargingSessionRequestValidator;
use Symfony\Component\Routing\Attribute\Route;

class ChargingSessionRequestController extends AbstractController
{
    #[Route('/charging-session-requests', name: 'charging_session_request', methods: ['POST'])]
    public function startChargingSession(
        Request $request,
        MessageBusInterface $commandBus,
        ChargingSessionRequestValidator $chargingSessionRequestValidator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        try {
            $chargingSessionRequestValidator->validate($data);
            $command = new StartChargingSessionRequestCommand(
                $data['stationId'],
                $data['driverId'],
                $data['callbackUrl']
            );

            $commandBus->dispatch($command);

            return $this->createJsonResponse([
                'status' => 'acknowledged',
                'message' => 'Charging Session request received'
            ], Response::HTTP_ACCEPTED);

        } catch (BadRequestException $e) {
            return $this->createJsonResponse(
                json_decode($e->getMessage(), true),
                Response::HTTP_BAD_REQUEST
            );
        }
        catch (ExceptionInterface $e) {
            return $this->createJsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
        catch (Exception $e) {
            return $this->createJsonResponse([
                'status' => 'error',
                'message' => 'An unexpected error occurred.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createJsonResponse(array $data, int $statusCode): JsonResponse
    {
        return new JsonResponse($data, $statusCode);
    }
}
