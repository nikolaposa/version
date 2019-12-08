<?php

declare(strict_types=1);

namespace Version\Exception;

use DomainException;

class InvalidConstraintStringException extends DomainException implements ExceptionInterface
{
    public static function forEmptyString(): self
    {
        return new self('Constraint string must not be empty');
    }

    public static function forUnparsableString(string $constraintString): self
    {
        return new self(sprintf(
            "Constraint string: '%s' seems to be invalid and it cannot be parsed",
            $constraintString
        ));
    }
}
