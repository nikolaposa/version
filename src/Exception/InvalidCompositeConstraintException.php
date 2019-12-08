<?php

declare(strict_types=1);

namespace Version\Exception;

use DomainException;

class InvalidCompositeConstraintException extends DomainException implements ExceptionInterface
{
    public static function forUnsupportedOperator(string $operator): self
    {
        return new self(sprintf('Unsupported composite constraint operator: %s', $operator));
    }
}
