<?php

declare(strict_types=1);

namespace KynxTest\ApiKey;

use Kynx\ApiKey\ApiKey;
use Kynx\ApiKey\InvalidArgumentException;
use Kynx\ApiKey\KeyGenerator;
use Kynx\ApiKey\RandomStringInterface;
use PHPUnit\Framework\TestCase;

use function str_pad;

/**
 * @uses \Kynx\ApiKey\ApiKey
 * @uses \Kynx\ApiKey\InvalidArgumentException
 * @uses \Kynx\ApiKey\RandomString
 *
 * @covers \Kynx\ApiKey\KeyGenerator
 */
final class KeyGeneratorTest extends TestCase
{
    private KeyGenerator $generator;
    protected function setUp(): void
    {
        parent::setUp();

        $randomString    = new class () implements RandomStringInterface
        {
            public function generate(int $length): string
            {
                return str_pad('', $length, 'a');
            }
        };
        $this->generator = new KeyGenerator('phpunit', 8, 16, $randomString);
    }

    public function testConstructInvalidPrefixThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'' is not a valid prefix");
        new KeyGenerator('');
    }

    public function testConstructInvalidIdentityLengthThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Identifiers should be at least 8 characters");
        new KeyGenerator('foo', 1);
    }

    public function testConstructInvalidSecretLengthThrowsExcpetion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Secrets should be at least 16 characters");
        new KeyGenerator('foo', 8, 1);
    }

    public function testGenerateReturnsValidApiKey(): void
    {
        $expected = new ApiKey('phpunit', 'aaaaaaaa', 'aaaaaaaaaaaaaaaa');
        $actual   = $this->generator->generate();
        self::assertEquals($expected, $actual);
    }

    /**
     * @dataProvider parseProvider
     */
    public function testParse(string $apiKey, ?ApiKey $expected): void
    {
        $actual = $this->generator->parse($apiKey);
        self::assertEquals($expected, $actual);
    }

    /**
     * @return array<string, array{0: string, 1: ApiKey|null}>
     */
    public static function parseProvider(): array
    {
        $valid = new ApiKey('phpunit', 'aaaaaaaa', 'aaaaaaaaaaaaaaaa');
        return [
            'empty'              => ['', null],
            'no prefix'          => ['aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2', null],
            'invalid prefix'     => ['popunit_aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2', null],
            'invalid identifier' => ['phpunit_aaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2', null],
            'invalid secret'     => ['phpunit_aaaaaaaa_aaaaaaaaaaaaaaa_8e3c92a2', null],
            'trailing chars'     => ['phpunit_aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2_a', null],
            'invalid checksum'   => ['phpunit_aaaaaaaa_aaaaaaaaaaaaaaab_8e3c92a2', null],
            'valid'              => ['phpunit_aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2', $valid],
        ];
    }
}
