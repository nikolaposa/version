<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Constraint\Parser;

use Version\Constraint\ConstraintInterface;
use Version\Exception\InvalidConstraintStringException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
interface ParserInterface
{
    /**
     * @param string $constraintString
     * @throws InvalidConstraintStringException
     * @return ConstraintInterface
     */
    public function parse($constraintString);
}
