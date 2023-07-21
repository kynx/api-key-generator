<?php

declare(strict_types=1);

namespace Kynx\ApiKey;

interface KeyGeneratorInterface
{
    /**
     * Returns API key
     */
    public function generate(): ApiKey;

    /**
     * Returns API key if string is syntactically correct
     */
    public function parse(string $apiKey): ?ApiKey;
}
