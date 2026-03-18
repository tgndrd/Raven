<?php

declare(strict_types=1);

namespace CHStudio\RavenTest\Http\Factory;

use CHStudio\Raven\Http\Factory\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stringable;

final class UriTest extends TestCase
{
    public function testItCanBeBuiltFromString(): void
    {
        $uri = new Uri('a string');
        self::assertInstanceOf(Stringable::class, $uri);
        self::assertSame('a string', $uri->__toString());
    }

    public function testItFailsToBuildFromArrayWithoutBase(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Uri(['parameters' => []]);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideItCanBeBuiltFromArrayCases')]
    public function testItCanBeBuiltFromArray($array, $expected): void
    {
        $uri = new Uri($array);
        self::assertSame($expected, $uri->__toString());
    }

    public static function provideItCanBeBuiltFromArrayCases(): iterable
    {
        yield [
            [
                'base' => 'http://param.host.int?value=key',
                'parameters' => [
                    'param' => 'value',
                    'int' => '0'
            ]], 'http://value.host.0?value=key'
        ];

        yield [
            [
                'base' => 'http://chstudio.fr?{name}={value}',
                'parameters' => [
                    '{name}' => 'keyword',
                    '{value}' => '%s'
            ]], 'http://chstudio.fr?keyword=%s'
        ];

        yield [
            [
                'base' => 'https://chstudio.fr'
            ], 'https://chstudio.fr'
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideItCantBeBuiltFromOtherValuesCases')]
    public function testItCantBeBuiltFromOtherValues($value): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Uri($value);
    }

    public static function provideItCantBeBuiltFromOtherValuesCases(): iterable
    {
        yield "Not an array" => [0];
        yield "Nothing in the parameters" => [[]];
        yield "No base but valid parameters" => [['parameters' => []]];
        yield [['base' => 'http://example.com', 'parameters' => 'not-an-array']];
        yield [['base' => 'http://example.com', 'parameters' => ['int' => 0]]];
        yield [null];
    }
}
