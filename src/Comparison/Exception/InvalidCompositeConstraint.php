<?php

declare(strict_types=1);

namespace Version\Comparison\Exception;

use DomainException;
use Version\Exception\VersionException;

class InvalidCompositeConstraint extends DomainException implements VersionException
{
    public static function unsupportedOperator(string $operator): self
    {
        return new self(sprintf('Unsupported composite constraint operator: %s', $operator));
    }
}
