<?php

declare(strict_types=1);

namespace Version\Exception;

use DomainException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class InvalidComparisonConstraintException extends DomainException implements ExceptionInterface
{
    public static function forOperator(string $operator) : self
    {
        return new self(sprintf('Unsupported operator: %s', $operator));
    }
}
