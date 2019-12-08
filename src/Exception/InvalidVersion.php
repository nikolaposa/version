<?php

declare(strict_types=1);

namespace Version\Exception;

use DomainException;

class InvalidVersion extends DomainException implements VersionException
{
    public static function negativeNumber(string $part, int $value): self
    {
        return new self(sprintf(
            '%s version must be positive integer, %s given',
            ucfirst($part),
            $value
        ));
    }
}
