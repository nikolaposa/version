<?php

declare(strict_types=1);

namespace Version\Comparison\Exception;

use InvalidArgumentException;
use Version\Exception\VersionException;

class InvalidConstraintString extends InvalidArgumentException implements VersionComparisonException
{
    public static function empty(): self
    {
        return new self('Comparison constraint string must not be empty');
    }

    public static function notParsable(string $constraintString): self
    {
        return new self(sprintf(
            "Comparison constraint string: '%s' is not valid and cannot be parsed",
            $constraintString
        ));
    }
}
