<?php

declare(strict_types=1);

namespace Kynx\ApiKey;

use Kynx\ApiKey\InvalidArgumentException;

use function sscanf;
use function strlen;

final readonly class KeyGenerator implements KeyGeneratorInterface
{
    private const MIN_IDENTIFIER_LENGTH = 8;
    private const MIN_SECRET_LENGTH     = 16;

    private string $format;

    public function __construct(
        private string $prefix,
        private int $identifierLength = 8,
        private int $secretLength = 16,
        private RandomStringInterface $randomString = new RandomString(),
    ) {
        if ($this->prefix === '') {
            throw InvalidArgumentException::invalidPrefix($this->prefix);
        }
        if ($this->identifierLength < self::MIN_IDENTIFIER_LENGTH) {
            throw InvalidArgumentException::invalidIdentifierLength(
                $this->identifierLength,
                self::MIN_IDENTIFIER_LENGTH
            );
        }
        if ($this->secretLength < self::MIN_SECRET_LENGTH) {
            throw InvalidArgumentException::invalidSecretLength($this->secretLength, self::MIN_SECRET_LENGTH);
        }

        $this->format = '%' . strlen($this->prefix) . 's'
            . '_%' . $this->identifierLength . 's'
            . '_%' . $this->secretLength . 's'
            . '_%8s%s';
    }

    public function generate(): ApiKey
    {
        return new ApiKey(
            $this->prefix,
            $this->randomString->generate($this->identifierLength),
            $this->randomString->generate($this->secretLength)
        );
    }

    public function parse(string $apiKey): ?ApiKey
    {
        $matched = sscanf($apiKey, $this->format, $prefix, $identifier, $secret, $checksum, $extra);
        if ($matched !== 4) {
            return null;
        }
        if ($prefix !== $this->prefix) {
            return null;
        }

        $parsed = new ApiKey((string) $prefix, (string) $identifier, (string) $secret);
        if (! $parsed->matches((string) $checksum)) {
            return null;
        }

        return $parsed;
    }
}
