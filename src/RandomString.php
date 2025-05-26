<?php

declare(strict_types=1);

namespace Kynx\ApiKey;

use Random\Randomizer;

final readonly class RandomString implements RandomStringInterface
{
    public const DEFAULT_CHARACTERS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_';

    private Randomizer $randomizer;

    public function __construct(
        private string $characters = self::DEFAULT_CHARACTERS
    ) {
        $this->randomizer = new Randomizer();
    }

    public function generate(int $length): string
    {
        return $this->randomizer->getBytesFromString($this->characters, $length);
    }

    public function getCharacters(): string
    {
        return $this->characters;
    }
}
