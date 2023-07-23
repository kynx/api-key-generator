<?php

declare(strict_types=1);

namespace KynxTest\ApiKey;

use Exception;
use Kynx\ApiKey\BcApiKey;
use Kynx\ApiKey\BcKeyGenerator;
use Kynx\ApiKey\InvalidArgumentException;
use Kynx\ApiKey\RandomStringInterface;
use PHPUnit\Framework\TestCase;

use function str_pad;

/**
 * @uses \Kynx\ApiKey\BcApiKey
 * @uses \Kynx\ApiKey\InvalidArgumentException
 * @uses \Kynx\ApiKey\RandomString
 *
 * @covers \Kynx\ApiKey\BcKeyGenerator
 * @psalm-suppress DeprecatedClass
 */
final class BcKeyGeneratorTest extends TestCase
{
    private BcKeyGenerator $generator;
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
        $this->generator = new BcKeyGenerator('phpunit', 8, 16, $randomString);
    }

    public function testConstructInvalidPrefixThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("'' is not a valid prefix");
        new BcKeyGenerator('');
    }

    public function testConstructInvalidIdentityLengthThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Identifiers should be at least 8 characters");
        new BcKeyGenerator('foo', 1);
    }

    public function testConstructInvalidSecretLengthThrowsExcpetion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Secrets should be at least 16 characters");
        new BcKeyGenerator('foo', 8, 1);
    }

    public function testGenerateThrowException(): void
    {
        self::expectException(Exception::class);
        $this->generator->generate();
    }

    /**
     * @dataProvider parseProvider
     */
    public function testParse(string $apiKey, ?BcApiKey $expected): void
    {
        $actual = $this->generator->parse($apiKey);
        self::assertEquals($expected, $actual);
    }

    /**
     * @return array<string, array{0: string, 1: BcApiKey|null}>
     */
    public static function parseProvider(): array
    {
        $valid = new BcApiKey('phpunit', 'aaaaaaaa', 'aaaaaaaaaaaaaaaa');
        return [
            'empty'              => ['', null],
            'no prefix'          => ['aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2', null],
            'invalid prefix'     => ['popunit_aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2', null],
            'invalid identifier' => ['phpunit_aaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2', null],
            'invalid secret'     => ['phpunit_aaaaaaaa_aaaaaaaaaaaaaaa_8e3c92a2', null],
            'trailing chars'     => ['phpunit_aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2_a', null],
            'trailing w space'   => ['phpunit_aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2 a', null],
            'invalid checksum'   => ['phpunit_aaaaaaaa_aaaaaaaaaaaaaaab_8e3c92a2', null],
            'valid'              => ['phpunit_aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2', $valid],
            'trim'               => [" phpunit_aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2\t\n", $valid],
        ];
    }
}
