<?php

declare(strict_types=1);

namespace Version\Comparison\Exception;

use DomainException;
use Version\Exception\VersionException;

class InvalidConstraintString extends DomainException implements VersionException
{
    public static function empty(): self
    {
        return new self('Comparision constraint string must not be empty');
    }

    public static function notParsable(string $constraintString): self
    {
        return new self(sprintf(
            "Comparision constraint string: '%s' is not valid and cannot be parsed",
            $constraintString
        ));
    }
}
