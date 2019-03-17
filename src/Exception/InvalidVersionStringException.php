<?php

declare(strict_types=1);

namespace Version\Exception;

use DomainException;

class InvalidVersionStringException extends DomainException implements ExceptionInterface
{
    /** @var string */
    protected $versionString;

    public static function forVersionString(string $versionString) : self
    {
        $exception = new self(sprintf("Version string '%s' is not valid and cannot be parsed", $versionString));
        $exception->versionString = $versionString;

        return $exception;
    }

    public function getVersionString() : string
    {
        return $this->versionString;
    }
}
