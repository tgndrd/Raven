<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Http\Factory;

use CHStudio\Raven\Http\Factory\Headers;
use InvalidArgumentException;
use IteratorAggregate;
use PHPUnit\Framework\TestCase;
use Stringable;

final class HeadersTest extends TestCase
{
    public function testItCanBeBuiltFromString(): void
    {
        $uri = new Headers([]);
        self::assertInstanceOf(Stringable::class, $uri);
        self::assertInstanceOf(IteratorAggregate::class, $uri);
    }

    public function testItCanBeFilledAndChecked(): void
    {
        $headers = new Headers(['Content-Type' => 'application/json']);

        self::assertTrue($headers->has('Content-Type'));
        self::assertTrue($headers->has('content-type'));
        self::assertTrue($headers->has('content-Type'));

        $expected = ['application/json'];

        self::assertSame($expected, $headers->get('Content-Type'));
        self::assertSame($expected, $headers->get('content-type'));
        self::assertSame($expected, $headers->get('content-Type'));
        self::assertSame('application/json', $headers->first('content-Type'));

        self::assertSame([], $headers->get('Non-Existing-Header'));

        $headers->append('content-type', 'other/type');

        self::assertSame(['application/json', 'other/type'], $headers->get('Content-Type'));

        $iterator = iterator_to_array($headers);

        self::assertArrayHasKey('content-type', $iterator);
        self::assertSame(['application/json', 'other/type'], $iterator['content-type']);
    }

    public function testItCanBeSerializedToString(): void
    {
        $headers = new Headers([
            'Content-Type' => 'application/json',
            'A' => ['B', 'C']
        ]);

        self::assertSame(<<<STRING
        Content-Type: application/json
        A: B
        A: C

        STRING, (string) $headers);

        $headers->append('A', 'D');

        self::assertSame(<<<STRING
        Content-Type: application/json
        A: B
        A: C
        A: D

        STRING, (string) $headers);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideItCantBeBuiltFromOtherValuesCases')]
    public function testItCantBeBuiltFromOtherValues($headers): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Headers($headers);
    }

    public static function provideItCantBeBuiltFromOtherValuesCases(): iterable
    {
        yield "Headers aren't array" => [0];
        yield "Header name not a string" => [[0 => 'Value']];
        yield "Header value not a string" => [['name' => 0]];
        yield "Header value not a string inside array" => [['name' => [0]]];
    }
}
