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

use Version\Constraint\CompositeConstraint;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
abstract class AbstractMultiPartParser extends AbstractParser
{
    const OPERATOR_OR = '||';

    protected $constraintParts = [];

    protected function doParse()
    {
        if (!$this->isMultiPartConstraint()) {
            return $this->buildConstraintFromStringUnit($this->constraintString);
        }

        $this->splitConstraintParts();

        return $this->buildCompositeConstraint();
    }

    protected function isMultiPartConstraint()
    {
        return (false !== strpos($this->constraintString, ' '));
    }

    protected function splitConstraintParts()
    {
        $this->constraintParts = explode(' ', $this->constraintString);
    }

    protected function buildCompositeConstraint()
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
                    $compositeOrConstraints[] = CompositeConstraint::fromAndConstraints($compositeAndConstraints);
                    $compositeAndConstraints = [];
                    break;
            }
        }

        if (!empty($compositeOrConstraints)) {
            if (empty($compositeAndConstraints)) {
                //invalid OR constraint; no right side
                $this->error();
            }

            $compositeOrConstraints[] = CompositeConstraint::fromAndConstraints($compositeAndConstraints);

            return CompositeConstraint::fromOrConstraints($compositeOrConstraints);
        }

        return CompositeConstraint::fromAndConstraints($compositeAndConstraints);
    }

    protected function isOperator($constraintPart)
    {
        return in_array($constraintPart, [
            self::OPERATOR_OR,
        ]);
    }
}
