<?php

declare(strict_types=1);

namespace Version\Exception;

use InvalidArgumentException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class InvalidVersionElementException extends InvalidArgumentException implements ExceptionInterface
{
    public static function forElement(string $part) : self
    {
        return new self(sprintf(
            '%s version must be non-negative integer',
            ucfirst($part)
        ));
    }
}
