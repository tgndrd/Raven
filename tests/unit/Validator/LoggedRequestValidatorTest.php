<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Validator;

use CHStudio\Raven\Validator\LoggedRequestValidator;
use CHStudio\Raven\Validator\RequestValidatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;

final class LoggedRequestValidatorTest extends TestCase
{
    public function testItCanBeBuilt(): void
    {
        $decorated = self::createStub(RequestValidatorInterface::class);
        $logger = self::createStub(LoggerInterface::class);

        $factory = new LoggedRequestValidator($logger, $decorated);

        self::assertInstanceOf(RequestValidatorInterface::class, $factory);
    }

    public function testItLogsRequestAtDebugLevel(): void
    {
        $decorated = $this->createMock(RequestValidatorInterface::class);
        $uri = $this->createMock(UriInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $logger = $this->createMock(LoggerInterface::class);

        $uri
            ->expects(self::exactly(2))
            ->method('__toString')
            ->willReturn('https://chstudio.fr');
        $request
            ->expects(self::exactly(2))
            ->method('getMethod')
            ->willReturn('GET');
        $request
            ->expects(self::exactly(2))
            ->method('getUri')
            ->willReturn($uri);

        $invokedCount = self::exactly(2);
        $logger
            ->expects($invokedCount)
            ->method('debug')
            ->willReturnCallback(function ($parameters) use ($invokedCount): void {
                $expectedParameters = match ($invokedCount->numberOfInvocations()) {
                    1 => 'Start testing Request: [GET] https://chstudio.fr',
                    2 => 'Finish testing Request: [GET] https://chstudio.fr',
                };
                $this->assertSame($expectedParameters, $parameters);
            });

        $decorated
            ->expects(self::once())
            ->method('validate')
            ->with($request);

        (new LoggedRequestValidator($logger, $decorated))->validate($request);
    }
}
