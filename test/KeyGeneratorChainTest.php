<?php

declare(strict_types=1);

namespace KynxTest\ApiKey;

use Kynx\ApiKey\ApiKey;
use Kynx\ApiKey\KeyGeneratorChain;
use Kynx\ApiKey\KeyGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Kynx\ApiKey\ApiKey
 *
 * @covers \Kynx\ApiKey\KeyGeneratorChain
 */
final class KeyGeneratorChainTest extends TestCase
{
    private KeyGeneratorInterface&MockObject $primary;
    private KeyGeneratorInterface&MockObject $fallback;
    private KeyGeneratorChain $chain;

    protected function setUp(): void
    {
        parent::setUp();

        $this->primary  = $this->createMock(KeyGeneratorInterface::class);
        $this->fallback = $this->createMock(KeyGeneratorInterface::class);
        $this->chain    = new KeyGeneratorChain($this->primary, $this->fallback);
    }

    public function testGenerateReturnsPrimaryApiKey(): void
    {
        $expected = new ApiKey('phpunit', 'aaaaaaaa', 'aaaaaaaaaaaaaaaa');
        $this->primary->method('generate')
            ->willReturn($expected);
        $this->fallback->expects(self::never())
            ->method('generate');

        $actual = $this->chain->generate();
        self::assertEquals($expected, $actual);
    }

    public function testParseParsesPrimary(): void
    {
        $expected = new ApiKey('phpunit', 'aaaaaaaa', 'aaaaaaaaaaaaaaaa');
        $this->primary->method('parse')
            ->willReturn($expected);
        $this->fallback->expects(self::never())
            ->method('parse');

        $actual = $this->chain->parse('phpunit_aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2');
        self::assertEquals($expected, $actual);
    }

    public function testParseUsesFallback(): void
    {
        $expected = new ApiKey('phpunit', 'aaaaaaaa', 'aaaaaaaaaaaaaaaa');
        $this->primary->method('parse')
            ->willReturn(null);
        $this->fallback->method('parse')
            ->willReturn($expected);

        $actual = $this->chain->parse('phpunit_aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2');
        self::assertEquals($expected, $actual);
    }

    public function testParseReturnsNull(): void
    {
        $this->primary->method('parse')
            ->willReturn(null);
        $this->fallback->method('parse')
            ->willReturn(null);

        $actual = $this->chain->parse('phpunit_aaaaaaaa_aaaaaaaaaaaaaaaa_8e3c92a2');
        self::assertNull($actual);
    }
}
