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
        $secret     = 'aaaaaaaaaaaaaaaaaaaaaaaa';
        $checksum   = '4c890bb3';

        $apiKey = new ApiKey($prefix, $identifier, $secret);
        self::assertSame($prefix, $apiKey->getPrefix());
        self::assertSame($identifier, $apiKey->getIdentifier());
        self::assertSame($secret, $apiKey->getSecret());
        self::assertSame($checksum, $apiKey->getChecksum());
    }

    public function testMatchesReturnsTrue(): void
    {
        $apiKey = new ApiKey('phpunit', 'aaaaaaaa', 'aaaaaaaaaaaaaaaaaaaaaaaa');
        $actual = $apiKey->matches('4c890bb3');
        self::assertTrue($actual);
    }

    public function testMatchesReturnsFalse(): void
    {
        $apiKey = new ApiKey('phpunit', 'aaaaaaaa', 'Zaaaaaaaaaaaaaaaaaaaaaaa');
        $actual = $apiKey->matches('4c890bb3');
        self::assertFalse($actual);
    }

    public function testGetKeyReturnsKey(): void
    {
        $expected = 'phpunit_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa_4c890bb3';
        $apiKey   = new ApiKey('phpunit', 'aaaaaaaa', 'aaaaaaaaaaaaaaaaaaaaaaaa');
        $actual   = $apiKey->getKey();
        self::assertSame($expected, $actual);
    }

    public function testGetRegexpReturnsValidRegexp(): void
    {
        $expected = '#^a\\.\\#_(?P<identifier>[a\?\(]{8})(?P<secret>[a\?\(]{24})_(?P<checksum>[0-9a-f]{8})$#';
        $actual   = ApiKey::getRegExp('a?(', 'a.#', 8, 24);
        self::assertSame($expected, $actual);

        $prefix     = 'a.#';
        $identifier = 'a?(a?(a?';
        $secret     = 'a?(a?(a?(a?(a?(a?(a?(a?(';
        $checksum   = 'f329e146';
        $key        = sprintf('%s_%s%s_%s', $prefix, $identifier, $secret, $checksum);
        $matched    = preg_match($actual, $key, $matches);
        self::assertSame(1, $matched);

        self::assertSame($identifier, $matches['identifier']);
        self::assertSame($secret, $matches['secret']);
        self::assertSame($checksum, $matches['checksum']);
    }
}
