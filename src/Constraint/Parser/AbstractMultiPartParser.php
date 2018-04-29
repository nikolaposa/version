<?php

declare(strict_types=1);

namespace Version\Constraint\Parser;

use Version\Constraint\CompositeConstraint;
use Version\Constraint\ConstraintInterface;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
abstract class AbstractMultiPartParser extends AbstractParser
{
    protected const OPERATOR_OR = '||';

    /**
     * @var array
     */
    protected $constraintParts = [];

    protected function doParse() : ConstraintInterface
    {
        if (! $this->isMultiPartConstraint()) {
            return $this->buildConstraintFromStringUnit($this->constraintString);
        }

        $this->splitConstraintParts();

        return $this->buildCompositeConstraint();
    }

    protected function isMultiPartConstraint() : bool
    {
        return (false !== strpos($this->constraintString, ' '));
    }

    protected function splitConstraintParts() : void
    {
        $this->constraintParts = explode(' ', $this->constraintString);
    }

    protected function buildCompositeConstraint() : ConstraintInterface
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
                    $compositeOrConstraints[] = CompositeConstraint::fromAndConstraints(...$compositeAndConstraints);
                    $compositeAndConstraints = [];
                    break;
            }
        }

        if (!empty($compositeOrConstraints)) {
            if (empty($compositeAndConstraints)) {
                //invalid OR constraint; no right side
                $this->error();
            }

            $compositeOrConstraints[] = CompositeConstraint::fromAndConstraints(...$compositeAndConstraints);

            return CompositeConstraint::fromOrConstraints(...$compositeOrConstraints);
        }

        return CompositeConstraint::fromAndConstraints(...$compositeAndConstraints);
    }

    protected function isOperator(string $constraintPart) : bool
    {
        return in_array($constraintPart, [
            self::OPERATOR_OR,
        ], true);
    }
}
