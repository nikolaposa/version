<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Exception;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class InvalidVersionStringException extends \DomainException implements Exception
{
    /**
     * @var string
     */
    protected $versionString;

    public static function forVersionString($versionString)
    {
        $exception = new self(sprintf("Version string '%s' is not valid and cannot be parsed", $versionString));
        $exception->versionString = $versionString;

        return $exception;
    }

    public function getVersionString()
    {
        return $this->versionString;
    }
}
