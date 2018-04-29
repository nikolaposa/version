<?php

declare(strict_types=1);

namespace Version\Exception;

use DomainException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class InvalidCompositeConstraintException extends DomainException implements ExceptionInterface
{
    public static function forType(string $type) : self
    {
        return new self(sprintf('Unsupported type: %s', $type));
    }
}
