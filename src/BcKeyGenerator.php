<?php

declare(strict_types=1);

namespace Kynx\ApiKey;

use Exception;

use function preg_match;
use function trim;

/**
 * @deprecated Backwards-compatibility for 1.x-2.x migration. Will be removed in next major.
 *
 * @psalm-suppress UnusedProperty
 */
final readonly class BcKeyGenerator implements KeyGeneratorInterface
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

        /** @psalm-suppress DeprecatedClass */
        $this->regexp = BcApiKey::getRegExp(
            $this->randomString->getCharacters(),
            $this->prefix,
            $this->identifierLength,
            $this->secretLength
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public function generate(): ApiKey
    {
        throw new Exception("This class is for parsing 1.x keys only and cannot generate new keys");
    }

    /**
     * @psalm-suppress DeprecatedClass
     */
    public function parse(string $apiKey): ?BcApiKey
    {
        if (! preg_match($this->regexp, trim($apiKey), $matches)) {
            return null;
        }

        $parsed = new BcApiKey($this->prefix, $matches['identifier'], $matches['secret']);
        return $parsed->matches($matches['checksum']) ? $parsed : null;
    }
}
