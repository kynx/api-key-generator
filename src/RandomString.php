<?php

declare(strict_types=1);

namespace Kynx\ApiKey;

use RandomLib\Factory;
use RandomLib\Generator;
use SecurityLib\Strength;

final readonly class RandomString implements RandomStringInterface
{
    public const DEFAULT_CHARACTERS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';

    private Generator $generator;

    public function __construct(
        private string $characters = self::DEFAULT_CHARACTERS
    ) {
        $this->generator = (new Factory())->getGenerator(new Strength(Strength::MEDIUM));
    }

    public function generate(int $length): string
    {
        return $this->generator->generateString($length, $this->characters);
    }
}
