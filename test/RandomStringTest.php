<?php

declare(strict_types=1);

namespace KynxTest\ApiKey;

use Kynx\ApiKey\RandomString;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Kynx\ApiKey\RandomString
 */
final class RandomStringTest extends TestCase
{
    /**
     * @dataProvider generateProvider
     */
    public function testGenerate(int $length, ?string $characters): void
    {
        if ($characters === null) {
            $expected     = '#[' . RandomString::DEFAULT_CHARACTERS . ']{' . $length . '}#';
            $randomString = new RandomString();
        } else {
            $expected     = '#[' . $characters . ']{' . $length . '}#';
            $randomString = new RandomString($characters);
        }

        $generated = $randomString->generate($length);
        self::assertMatchesRegularExpression($expected, $generated);
    }

    /**
     * @return array<string, array{0: int, 1: string|null}>
     */
    public static function generateProvider(): array
    {
        return [
            'length'     => [100, null],
            'characters' => [100, 'aBbB12'],
        ];
    }
}
