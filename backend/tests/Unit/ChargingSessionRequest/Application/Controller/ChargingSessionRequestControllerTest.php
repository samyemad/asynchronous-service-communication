<?php

namespace App\Tests\Unit\ChargingSessionRequest\Application\Controller;

use App\ChargingSessionRequest\Application\Command\StartChargingSessionRequestCommand;
use App\ChargingSessionRequest\Application\Controller\ChargingSessionRequestController;
use App\ChargingSessionRequest\Application\Validator\ChargingSessionRequestValidator;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;

class ChargingSessionRequestControllerTest extends TestCase
{
    private ChargingSessionRequestController $controller;
    private MessageBusInterface $busMock;
    private ChargingSessionRequestValidator $validatorMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->controller = new ChargingSessionRequestController();
        $this->busMock = $this->createMock(MessageBusInterface::class);
        $this->validatorMock = $this->createMock(ChargingSessionRequestValidator::class);
    }

    private function createRequest(array $data): Request
    {
        return new Request([], [], [], [], [], [], json_encode($data));
    }

    private function validData(): array
    {
        return [
            'stationId' => '123e4567-e89b-12d3-a456-426614174000',
            'driverId' => 'a1H_G6H8j1AAR9ZW.0vDJXKtTq2FyL',
            'callbackUrl' => 'https://callback.example.com',
        ];
    }

    /**
     * @throws Exception
     */
    public function testStartChargingSessionWithValidData(): void
    {
        $data = $this->validData();
        $request = $this->createRequest($data);

        $this->validatorMock->expects($this->once())->method('validate')->with($data);

        $this->busMock->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(StartChargingSessionRequestCommand::class))
            ->willReturnCallback(fn($command) => new Envelope($command));

        $response = $this->controller->startChargingSession($request, $this->busMock, $this->validatorMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_ACCEPTED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'status' => 'acknowledged',
            'message' => 'Charging Session request received',
        ]), $response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testStartChargingSessionWithInvalidData(): void
    {
        $invalidData = ['stationId' => '', 'driverId' => '', 'callbackUrl' => ''];
        $request = $this->createRequest($invalidData);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->willThrowException(new BadRequestException(json_encode([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => ['stationId' => 'This value should not be blank.'],
            ])));

        $this->busMock->expects($this->never())->method('dispatch');

        $response = $this->controller->startChargingSession($request, $this->busMock, $this->validatorMock);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => ['stationId' => 'This value should not be blank.'],
        ]), $response->getContent());
    }

    /**
     * @throws Exception
     */
    public function testStartChargingSessionThrowsMessengerException(): void
    {
        $data = $this->validData();
        $request = $this->createRequest($data);

        $this->validatorMock->expects($this->once())->method('validate')->with($data);

        $this->busMock->expects($this->once())
            ->method('dispatch')
            ->willThrowException(new class extends \RuntimeException implements ExceptionInterface {
                public function __construct()
                {
                    parent::__construct('Dispatch error');
                }
            });

        $response = $this->controller->startChargingSession($request, $this->busMock, $this->validatorMock);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode([
            'status' => 'error',
            'message' => 'Dispatch error',
        ]), $response->getContent());
    }
}
