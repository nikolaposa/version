<?php

declare(strict_types=1);

namespace Version\Comparison\Constraint;

use Version\Exception\VersionException;
use Version\Comparison\Exception\InvalidConstraintString;
use Version\Version;

class OperationConstraintParser
{
    public const OPERATOR_OR = '||';

    /** @var string */
    protected $constraintString;

    /** @var array */
    protected $constraintParts = [];

    /**
     * @param string $constraintString
     * @return OperationConstraint|CompositeConstraint
     */
    public function parse(string $constraintString)
    {
        $constraintString = trim($constraintString);

        if ('' === $constraintString) {
            throw InvalidConstraintString::empty();
        }

        $this->constraintString = $constraintString;

        if (! $this->isMultiPartConstraint()) {
            return $this->buildConstraint($this->constraintString);
        }

        $this->splitConstraintParts();

        return $this->buildCompositeConstraint();
    }

    protected function isMultiPartConstraint(): bool
    {
        return (false !== strpos($this->constraintString, ' '));
    }

    protected function splitConstraintParts(): void
    {
        $constraintParts = explode(' ', $this->constraintString);
        $this->constraintParts = array_map('trim', $constraintParts);
    }

    protected function buildConstraint(string $constraintPart): OperationConstraint
    {
        [$operator, $operandString] = array_values($this->parseConstraint($constraintPart));

        if (empty($operandString)) {
            $this->error();
        }

        try {
            return new OperationConstraint(
                $operator ?: OperationConstraint::OPERATOR_EQ,
                Version::fromString($operandString)
            );
        } catch (VersionException $ex) {
            $this->error();
        }
    }

    protected function parseConstraint(string $constraintStringUnit): array
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

    protected function buildCompositeConstraint(): CompositeConstraint
    {
        $compositeAndConstraints = $compositeOrConstraints = [];

        foreach ($this->constraintParts as $constraintPart) {
            if (self::OPERATOR_OR === $constraintPart) {
                $compositeOrConstraints[] = CompositeConstraint::and(...$compositeAndConstraints);
                $compositeAndConstraints = []; //reset collected AND constraints
            } else {
                $compositeAndConstraints[] = $this->buildConstraint($constraintPart);
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

    protected function error(): void
    {
        throw InvalidConstraintString::notParsable($this->constraintString);
    }
}
