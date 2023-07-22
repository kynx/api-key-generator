<?php

declare(strict_types=1);

namespace Kynx\ApiKey;

final readonly class KeyGeneratorChain implements KeyGeneratorInterface
{
    /** @var array<array-key, KeyGeneratorInterface> */
    private array $fallbacks;

    public function __construct(
        private KeyGeneratorInterface $primary,
        KeyGeneratorInterface ...$fallbacks
    ) {
        $this->fallbacks = $fallbacks;
    }

    public function generate(): ApiKey
    {
        return $this->primary->generate();
    }

    public function parse(string $apiKey): ?ApiKey
    {
        return $this->primary->parse($apiKey) ?? $this->fallback($apiKey);
    }

    private function fallback(string $apiKey): ?ApiKey
    {
        foreach ($this->fallbacks as $fallback) {
            if (null !== ($key = $fallback->parse($apiKey))) {
                return $key;
            }
        }

        return null;
    }
}
