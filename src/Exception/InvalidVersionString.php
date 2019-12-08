<?php

declare(strict_types=1);

namespace Version\Exception;

use InvalidArgumentException;

class InvalidVersionString extends InvalidArgumentException implements VersionException
{
    /** @var string */
    protected $versionString;

    public static function notParsable(string $versionString): self
    {
        $exception = new self(sprintf("Version string '%s' is not valid and cannot be parsed", $versionString));
        $exception->versionString = $versionString;

        return $exception;
    }

    public function getVersionString(): string
    {
        return $this->versionString;
    }
}
