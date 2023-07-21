<?php

declare(strict_types=1);

namespace KynxTest\ApiKey;

use Kynx\ApiKey\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\ApiKey\InvalidArgumentException
 */
final class InvalidArgumentExceptionTest extends TestCase
{
    public function testInvalidPrefix(): void
    {
        $expected  = "'foo' is not a valid prefix";
        $exception = InvalidArgumentException::invalidPrefix('foo');
        $actual    = $exception->getMessage();
        self::assertSame($expected, $actual);
    }

    public function testInvalidIdentifierLength(): void
    {
        $expected  = "Identifiers should be at least 8 characters; 1 provided";
        $exception = InvalidArgumentException::invalidIdentifierLength(1, 8);
        $actual    = $exception->getMessage();
        self::assertSame($expected, $actual);
    }

    public function testInvalidSecretLength(): void
    {
        $expected  = "Secrets should be at least 32 characters; 1 provided";
        $exception = InvalidArgumentException::invalidSecretLength(1, 32);
        $actual    = $exception->getMessage();
        self::assertSame($expected, $actual);
    }
}
