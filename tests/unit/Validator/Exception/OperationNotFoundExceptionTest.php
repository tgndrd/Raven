<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Validator\Exception;

use CHStudio\Raven\Validator\Exception\OperationNotFoundException;
use CHStudio\Raven\Validator\Exception\ValidationException;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class OperationNotFoundExceptionTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $uri = $this->createMock(UriInterface::class);
        $uri
            ->expects(self::once())
            ->method('__toString')
            ->willReturn('http://uri');

        $request = $this->createMock(RequestInterface::class);
        $request
            ->expects(self::once())
            ->method('getMethod')
            ->willReturn('GET');
        $request
            ->expects(self::once())
            ->method('getUri')
            ->willReturn($uri);

        $exception = new OperationNotFoundException($request, new Exception('Error'));

        self::assertInstanceOf(ValidationException::class, $exception);
        self::assertStringContainsString('[GET] http://uri', $exception->getMessage());
    }
}
