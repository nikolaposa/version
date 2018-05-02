<?php

declare(strict_types=1);

namespace Version\Exception;

use DomainException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class InvalidVersionException extends DomainException implements ExceptionInterface
{
    public static function forNumber(string $name, int $value) : self
    {
        return new self(sprintf(
            '%s version must be non-negative integer, %s given',
            ucfirst($name),
            $value
        ));
    }
}
