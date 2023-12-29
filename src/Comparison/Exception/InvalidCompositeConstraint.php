<?php

declare(strict_types=1);

namespace Version\Comparison\Exception;

use InvalidArgumentException;
use Version\Exception\VersionException;

class InvalidCompositeConstraint extends InvalidArgumentException implements VersionComparisonException
{
    public static function unsupportedOperator(string $operator): self
    {
        return new self(sprintf('Unsupported composite constraint operator: %s', $operator));
    }
}
