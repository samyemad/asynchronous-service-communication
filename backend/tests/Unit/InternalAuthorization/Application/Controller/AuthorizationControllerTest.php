<?php

namespace App\Tests\Unit\InternalAuthorization\Application\Controller;

use App\InternalAuthorization\Application\Command\AuthorizeDriverCommand;
use App\InternalAuthorization\Application\Controller\AuthorizationController;
use App\InternalAuthorization\Application\Result\AuthorizationResult;
use App\InternalAuthorization\Application\Validator\AuthorizationValidator;
use App\InternalAuthorization\Application\Value\DriverTokenStatus;
use App\InternalAuthorization\Domain\Model\AuthorizationStatus;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AuthorizationControllerTest extends TestCase
{
    private AuthorizationController $controller;
    private MessageBusInterface $busMock;
    private AuthorizationValidator $validatorMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->controller = new AuthorizationController();
        $this->busMock = $this->createMock(MessageBusInterface::class);
        $this->validatorMock = $this->createMock(AuthorizationValidator::class);
    }

    private function createRequest(array $data): Request
    {
        return new Request([], [], [], [], [], [], json_encode($data));
    }

    private function createEnvelopeWithResult(array $data, AuthorizationResult $result): Envelope
    {
        return new Envelope(
            new AuthorizeDriverCommand($data['driverId'], $data['stationId']),
            [new HandledStamp($result, 'handler')]
        );
    }

    private function validDriverData(): array
    {
        return [
            'driverId' => 'a1H_G6H8j1AAR9ZW.0vDJXKtTq2FyL',
            'stationId' => '9673200c-05a1-4b2d-aa4c-9deb130981a2'
        ];
    }

    /**
     * @throws Exception
     */
    public function test_successful_authorization_with_token(): void
    {
        $data = $this->validDriverData();

        $result = new AuthorizationResult(
            $data['stationId'],
            'validDriverToken123',
            AuthorizationStatus::ALLOWED
        );

        $this->validatorMock->expects($this->once())->method('validate');
        $this->busMock->method('dispatch')->willReturn($this->createEnvelopeWithResult($data, $result));

        $response = $this->controller->authorizeDriver($this->createRequest($data), $this->busMock, $this->validatorMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(202, $response->getStatusCode());
        $this->assertEquals(AuthorizationStatus::ALLOWED, $content['status']);
        $this->assertEquals('validDriverToken123', $content['driver_token']);
    }

    /**
     * @throws Exception
     */
    public function test_failed_authorization_without_token(): void
    {
        $data = $this->validDriverData();

        $result = new AuthorizationResult(
            $data['stationId'],
            DriverTokenStatus::UNKNOWN,
            AuthorizationStatus::NOT_ALLOWED
        );

        $this->validatorMock->expects($this->once())->method('validate');
        $this->busMock->method('dispatch')->willReturn($this->createEnvelopeWithResult($data, $result));

        $response = $this->controller->authorizeDriver($this->createRequest($data), $this->busMock, $this->validatorMock);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(202, $response->getStatusCode());
        $this->assertEquals(AuthorizationStatus::NOT_ALLOWED, $content['status']);
        $this->assertEquals(DriverTokenStatus::UNKNOWN, $content['driver_token']);
    }

    /**
     * @throws Exception
     */
    public function test_missing_handled_stamp_returns_internal_error(): void
    {
        $data = $this->validDriverData();

        $this->validatorMock->expects($this->once())->method('validate');
        $this->busMock->method('dispatch')
            ->willReturn(new Envelope(new AuthorizeDriverCommand($data['driverId'], $data['stationId'])));

        $response = $this->controller->authorizeDriver($this->createRequest($data), $this->busMock, $this->validatorMock);

        $this->assertEquals(500, $response->getStatusCode());
    }

}
