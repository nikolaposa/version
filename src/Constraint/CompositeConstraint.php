<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Constraint;

use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class CompositeConstraint implements ConstraintInterface
{
    const TYPE_AND = 'AND';
    const TYPE_OR = 'OR';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var ConstraintInterface[]
     */
    protected $constraints;

    private function __construct()
    {
    }

    /**
     * @param string $type
     * @param array $constraints
     * @return self
     */
    public static function create($type, array $constraints)
    {
        if (!in_array($type, [self::TYPE_AND, self::TYPE_OR])) {

        }

        foreach ($constraints as $constraint) {
            if (!$constraint instanceof ConstraintInterface) {

            }
        }

        $compositeConstraint = new self();

        $compositeConstraint->type = $type;
        $compositeConstraint->constraints = $constraints;

        return $compositeConstraint;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * {@inheritDoc}
     */
    public function assert(Version $version)
    {
        foreach ($this->constraints as $constraint) {
            /* @var $constraint ConstraintInterface */

            $assert = $constraint->assert($version);

            if ($assert && $this->type == self::TYPE_OR) {
                return true;
            }

            if (!$assert && $this->type == self::TYPE_AND) {
                return false;
            }
        }

        return false;
    }
}
