<?php

declare(strict_types=1);

namespace Version\Constraint\Parser;

use Version\Constraint\ConstraintInterface;
use Version\Constraint\Constraint;
use Version\Version;
use Version\Exception\InvalidConstraintStringException;
use Version\Exception\ExceptionInterface;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
abstract class AbstractParser implements ParserInterface
{
    /**
     * @var string
     */
    protected $constraintString;

    public function parse(string $constraintString) : ConstraintInterface
    {
        $constraintString = trim($constraintString);

        if ('' === $constraintString) {
            throw InvalidConstraintStringException::forEmptyConstraintString();
        }

        $this->constraintString = $constraintString;

        return $this->doParse();
    }

    abstract protected function doParse() : ConstraintInterface;

    protected function error() : void
    {
        throw InvalidConstraintStringException::forConstraintString($this->constraintString);
    }

    protected function buildConstraintFromStringUnit(string $constraintStringUnit) : ConstraintInterface
    {
        [$operator, $operandString] = array_values($this->parseConstraintStringUnit($constraintStringUnit));

        if (empty($operandString)) {
            $this->error();
        }

        try {
            return Constraint::fromProperties(
                $operator ?: Constraint::OPERATOR_EQ,
                Version::fromString($operandString)
            );
        } catch (ExceptionInterface $ex) {
            $this->error();
        }
    }

    protected function parseConstraintStringUnit(string $constraintStringUnit) : array
    {
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
