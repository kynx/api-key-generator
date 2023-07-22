<?php

declare(strict_types=1);

namespace Kynx\ApiKey;

use function preg_match;
use function trim;

final readonly class KeyGenerator implements KeyGeneratorInterface
{
    private const MIN_IDENTIFIER_LENGTH = 8;
    private const MIN_SECRET_LENGTH     = 16;

    private string $regexp;

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

        $this->regexp = ApiKey::getRegExp(
            $this->randomString->getCharacters(),
            $this->prefix,
            $this->identifierLength,
            $this->secretLength
        );
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
        if (! preg_match($this->regexp, trim($apiKey), $matches)) {
            return null;
        }

        $parsed = new ApiKey($this->prefix, $matches['identifier'], $matches['secret']);
        return $parsed->matches($matches['checksum']) ? $parsed : null;
    }
}
