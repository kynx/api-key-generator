<?php

declare(strict_types=1);

namespace Kynx\ApiKey;

use Throwable;

final class InvalidArgumentException extends \InvalidArgumentException
{
    private function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function invalidPrefix(string $prefix): self
    {
        return new self("'$prefix' is not a valid prefix");
    }

    public static function invalidIdentifierLength(int $length, int $min): self
    {
        return new self("Identifiers should be at least $min characters; $length provided");
    }

    public static function invalidSecretLength(int $length, int $min): self
    {
        return new self("Secrets should be at least $min characters; $length provided");
    }
}
