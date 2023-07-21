<?php

declare(strict_types=1);

namespace Kynx\ApiKey;

use function hash;
use function sprintf;

final readonly class ApiKey
{
    private string $checksum;
    private string $key;

    public function __construct(
        private string $prefix,
        private string $identifier,
        private string $secret
    ) {
        $base           = $this->getBase();
        $this->checksum = $this->checksum($base);
        $this->key      = $base . $this->checksum;
    }

    public function matches(string $checksum): bool
    {
        return $this->checksum === $checksum;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getChecksum(): string
    {
        return $this->checksum;
    }

    private function getBase(): string
    {
        return sprintf(
            '%s_%s_%s_',
            $this->prefix,
            $this->identifier,
            $this->secret
        );
    }

    private function checksum(string $base): string
    {
        return hash('crc32b', $base);
    }
}
