<?php

declare(strict_types=1);

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
     *
     * @throws InvalidConstraintStringException
     *
     * @return ConstraintInterface
     */
    public function parse(string $constraintString) : ConstraintInterface;
}
