<?php
namespace App\Tests\Unit\Fakes;

use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class FakeServerException extends \RuntimeException implements ServerExceptionInterface
{
    public function __construct(string $message = 'Server error')
    {
        parent::__construct($message);
    }

    public function getResponse(): ResponseInterface
    {
        return new class implements ResponseInterface {
            public function getStatusCode(): int { return 500; }
            public function getHeaders(bool $throw = true): array { return []; }
            public function getContent(bool $throw = true): string { return ''; }
            public function toArray(bool $throw = true): array { return []; }
            public function cancel(): void {}
            public function getInfo(?string $type = null): mixed { return null; }
        };
    }
}
