<?php

declare(strict_types=1);

namespace Version\Exception;

use DomainException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class InvalidConstraintStringException extends DomainException implements ExceptionInterface
{
    public static function forEmptyConstraintString() : self
    {
        return new self('Constraint string must not be empty');
    }

    public static function forConstraintString(string $constraintString) : self
    {
        return new self(sprintf(
            "Constraint string: '%s' seems to be invalid and it cannot be parsed",
            $constraintString
        ));
    }
}
