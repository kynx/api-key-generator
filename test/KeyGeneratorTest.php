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

            public function getCharacters(): string
            {
                return 'a';
            }
        };
        $this->generator = new KeyGenerator('phpunit', 8, 24, $randomString);
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
        $this->expectExceptionMessage("Secrets should be at least 24 characters");
        new KeyGenerator('foo', 8, 1);
    }

    public function testGenerateReturnsValidApiKey(): void
    {
        $expected = new ApiKey('phpunit', 'aaaaaaaa', 'aaaaaaaaaaaaaaaaaaaaaaaa');
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
        $valid = new ApiKey('phpunit', 'aaaaaaaa', 'aaaaaaaaaaaaaaaaaaaaaaaa');
        return [
            'empty'              => ['', null],
            'no prefix'          => ['aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa_4c890bb3', null],
            'invalid prefix'     => ['popunit_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa_b6b4f8e4', null],
            'invalid identifier' => ['phpunit_Zaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa_ca5739e8', null],
            'invalid secret'     => ['phpunit_aaaaaaaaZaaaaaaaaaaaaaaaaaaaaaaa_bd5647e5', null],
            'trailing chars'     => ['phpunit_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa_4c890bb3_a', null],
            'trailing w space'   => ['phpunit_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa_4c890bb3 a', null],
            'invalid checksum'   => ['phpunit_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa_ca5739e8', null],
            'valid'              => ['phpunit_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa_4c890bb3', $valid],
            'trim'               => [" phpunit_aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa_4c890bb3\t\n", $valid],
        ];
    }
}
