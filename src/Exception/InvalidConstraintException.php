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
class InvalidConstraintException extends DomainException implements Exception
{
    public static function forOperator($operator)
    {
        return new self(sprintf('Unsupported operator: %s', $operator));
    }
}
