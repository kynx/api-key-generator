<?php

declare(strict_types=1);

namespace KynxTest\ApiKey;

use Kynx\ApiKey\ApiKey;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\ApiKey\ApiKey
 */
final class ApiKeyTest extends TestCase
{
    public function testConstructorSetsProperties(): void
    {
        $prefix     = 'phpunit';
        $identifier = 'aaaaaaaa';
        $secret     = 'aaaaaaaaaaaaaaaa';
        $checksum   = '8e3c92a2';

        $apiKey = new ApiKey($prefix, $identifier, $secret);
        self::assertSame($prefix, $apiKey->getPrefix());
        self::assertSame($identifier, $apiKey->getIdentifier());
        self::assertSame($secret, $apiKey->getSecret());
        self::assertSame($checksum, $apiKey->getChecksum());
    }

    public function testMatchesReturnsTrue(): void
    {
        $apiKey = new ApiKey('phpunit', 'aaaaaaaa', 'aaaaaaaaaaaaaaaa');
        $actual = $apiKey->matches('8e3c92a2');
        self::assertTrue($actual);
    }

    public function testMatchesReturnsFalse(): void
    {
        $apiKey = new ApiKey('phpunit', 'aaaaaaaa', 'aaaaaaaaaaaaaaab');
        $actual = $apiKey->matches('8e3c92a2');
        self::assertFalse($actual);
    }

    public function testGetKeyReturnsKey(): void
    {
        $expected = 'phpunit_aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2';
        $apiKey   = new ApiKey('phpunit', 'aaaaaaaa', 'aaaaaaaaaaaaaaaa');
        $actual   = $apiKey->getKey();
        self::assertSame($expected, $actual);
    }
}
