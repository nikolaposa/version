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
use Version\Constraint\ConstraintInterface;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class InvalidCompositeConstraintException extends DomainException implements Exception
{
    public static function forType($type)
    {
        return new self(sprintf('Unsupported type: %s', $type));
    }

    public static function forConstraint($constraint)
    {
        return new self(sprintf(
            'Constraints should be %s instances; %s given',
            ConstraintInterface::class,
            is_object($constraint) ? get_class($constraint) : gettype($constraint)
        ));
    }
}
