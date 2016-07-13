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

use DomainException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class InvalidConstraintStringException extends DomainException implements Exception
{
    public static function forInvalidType($constraintString)
    {
        return new self(sprintf(
            'Constraint string should be of type string; %s given',
            gettype($constraintString)
        ));
    }

    public static function forEmptyCostraintString()
    {
        return new self('Constraint string must not be empty');
    }

    public static function forConstraintString($constraintString)
    {
        return new self(sprintf(
            "Constraint string: '%s' seems to be invalid and it cannot be parsed",
            $constraintString
        ));
    }
}
