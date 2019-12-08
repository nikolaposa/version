<?php

declare(strict_types=1);

namespace Version\Comparison\Exception;

use DomainException;
use Version\Exception\VersionException;

class InvalidOperatorConstraint extends DomainException implements VersionException
{
    public static function unsupportedOperator(string $operator): self
    {
        return new self(sprintf('Unsupported constraint operator: %s', $operator));
    }
}
