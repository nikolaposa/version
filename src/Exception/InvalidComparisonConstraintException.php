<?php

declare(strict_types=1);

namespace Version\Exception;

use DomainException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class InvalidComparisonConstraintException extends DomainException implements ExceptionInterface
{
    public static function forUnsupportedOperator(string $operator) : self
    {
        return new self(sprintf('Unsupported comparison constraint operator: %s', $operator));
    }
}
