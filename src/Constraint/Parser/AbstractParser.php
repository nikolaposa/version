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
use Version\Constraint\Constraint;
use Version\Version;
use Version\Exception\InvalidConstraintStringException;
use Version\Exception\InvalidVersionStringException;
use Version\Exception\InvalidConstraintException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
abstract class AbstractParser implements ParserInterface
{
    /**
     * @var string
     */
    protected $constraintString;

    /**
     * {@inheritDoc}
     */
    public function parse($constraintString)
    {
        if (!is_string($constraintString)) {
            throw InvalidConstraintStringException::forInvalidType($constraintString);
        }

        $constraintString = trim($constraintString);

        if ($constraintString == '') {
            throw InvalidConstraintStringException::forEmptyCostraintString();
        }

        $this->constraintString = $constraintString;

        return $this->doParse();
    }

    /**
     * @return ConstraintInterface
     */
    abstract protected function doParse();

    protected function error()
    {
        throw InvalidConstraintStringException::forConstraintString($this->constraintString);
    }

    /**
     * @param string $constraintStringUnit
     * @return Constraint
     */
    protected function buildConstraintStringUnit($constraintStringUnit)
    {
        list($operator, $operandString) = array_values($this->parseConstraintStringUnit($constraintStringUnit));

        if (empty($operandString)) {
            $this->error();
        }

        $operand = null;
        try {
            $operand = Version::fromString($operandString);
        } catch (InvalidVersionStringException $ex) {
            $this->error();
        }

        if (empty($operator)) {
            $operator = Constraint::OPERATOR_EQ;
        }

        try {
            return Constraint::fromProperties($operator, $operand);
        } catch (InvalidConstraintException $ex) {
            $this->error();
        }
    }

    /**
     * @param string $constraintStringUnit
     * @return array
     */
    protected function parseConstraintStringUnit($constraintStringUnit)
    {
        $operator = $operand = '';

        $i = 0;
        while (isset($constraintStringUnit[$i]) && !ctype_digit($constraintStringUnit[$i])) {
            $i++;
        }

        $operator = substr($constraintStringUnit, 0, $i);
        $operand = substr($constraintStringUnit, $i);

        return [
            'operator' => $operator,
            'operand' => $operand,
        ];
    }
}
