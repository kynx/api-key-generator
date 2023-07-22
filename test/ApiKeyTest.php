<?php

declare(strict_types=1);

namespace KynxTest\ApiKey;

use Kynx\ApiKey\ApiKey;
use PHPUnit\Framework\TestCase;

use function preg_match;
use function sprintf;

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

    public function testGetRegexpReturnsValidRegexp(): void
    {
        $expected = '#^a\\.\\#_(?P<identifier>[a\?\(]{8})_(?P<secret>[a\?\(]{16})_(?P<checksum>[0-9a-f]{8})$#';
        $actual   = ApiKey::getRegExp('a?(', 'a.#', 8, 16);
        self::assertSame($expected, $actual);

        $prefix     = 'a.#';
        $identifier = 'a?(a?(a?';
        $secret     = 'a?(a?(a?(a?a?(a?';
        $checksum   = '99e78e35';
        $key        = sprintf('%s_%s_%s_%s', $prefix, $identifier, $secret, $checksum);
        $matched    = preg_match($actual, $key, $matches);
        self::assertSame(1, $matched);

        self::assertSame($identifier, $matches['identifier']);
        self::assertSame($secret, $matches['secret']);
        self::assertSame($checksum, $matches['checksum']);
    }
}
