<?php

declare(strict_types=1);

namespace Kynx\ApiKey;

interface RandomStringInterface
{
    /**
     * Returns random string of given length
     */
    public function generate(int $length): string;

    public function getCharacters(): string;
}
