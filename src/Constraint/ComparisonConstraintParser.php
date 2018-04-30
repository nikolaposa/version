<?php

declare(strict_types=1);

namespace Version\Constraint;

use Version\Exception\ExceptionInterface;
use Version\Exception\InvalidComparisonConstraintStringException;
use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class ComparisonConstraintParser
{
    public const OPERATOR_OR = '||';

    /**
     * @var string
     */
    protected $constraintString;

    /**
     * @var array
     */
    protected $constraintParts = [];

    public function parse(string $constraintString) : ConstraintInterface
    {
        $constraintString = trim($constraintString);

        if ('' === $constraintString) {
            throw InvalidComparisonConstraintStringException::forEmptyString();
        }

        $this->constraintString = $constraintString;

        if (! $this->isMultiPartConstraint()) {
            return $this->buildConstraintFromStringUnit($this->constraintString);
        }

        $this->splitConstraintParts();

        return $this->buildCompositeConstraint();
    }

    protected function buildConstraintFromStringUnit(string $constraintStringUnit) : ComparisonConstraint
    {
        [$operator, $operandString] = array_values($this->parseConstraintStringUnit($constraintStringUnit));

        if (empty($operandString)) {
            $this->error();
        }

        try {
            return new ComparisonConstraint(
                $operator ?: ComparisonConstraint::OPERATOR_EQ,
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

    protected function isMultiPartConstraint() : bool
    {
        return (false !== strpos($this->constraintString, ' '));
    }

    protected function splitConstraintParts() : void
    {
        $this->constraintParts = explode(' ', $this->constraintString);
    }

    protected function buildCompositeConstraint() : CompositeConstraint
    {
        $compositeAndConstraints = $compositeOrConstraints = [];

        foreach ($this->constraintParts as $constraintPart) {
            if (!$this->isOperator($constraintPart)) {
                $compositeAndConstraints[] = $this->buildConstraintFromStringUnit($constraintPart);
                continue;
            }

            $constraintOperator = $constraintPart;

            switch ($constraintOperator) {
                case self::OPERATOR_OR:
                    $compositeOrConstraints[] = CompositeConstraint::and(...$compositeAndConstraints);
                    $compositeAndConstraints = [];
                    break;
            }
        }

        if (!empty($compositeOrConstraints)) {
            if (empty($compositeAndConstraints)) {
                //invalid OR constraint; no right side
                $this->error();
            }

            $compositeOrConstraints[] = CompositeConstraint::and(...$compositeAndConstraints);

            return CompositeConstraint::or(...$compositeOrConstraints);
        }

        return CompositeConstraint::and(...$compositeAndConstraints);
    }

    protected function isOperator(string $constraintPart) : bool
    {
        return in_array($constraintPart, [
            self::OPERATOR_OR,
        ], true);
    }

    protected function error() : void
    {
        throw InvalidComparisonConstraintStringException::forUnparsableString($this->constraintString);
    }
}
