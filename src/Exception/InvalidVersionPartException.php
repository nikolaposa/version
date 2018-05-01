<?php

declare(strict_types=1);

namespace Version\Exception;

use InvalidArgumentException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class InvalidVersionPartException extends InvalidArgumentException implements ExceptionInterface
{
    public static function forPart(string $part) : self
    {
        return new self(sprintf('%s version must be non-negative integer', ucfirst($part)));
    }
}
