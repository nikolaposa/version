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
use Version\Exception\InvalidCompositeConstraintException;

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
    public static function fromProperties($type, array $constraints)
    {
        if (!in_array($type, [self::TYPE_AND, self::TYPE_OR])) {
            throw InvalidCompositeConstraintException::forType($type);
        }

        foreach ($constraints as $constraint) {
            if (!$constraint instanceof ConstraintInterface) {
                throw InvalidCompositeConstraintException::forConstraint($constraint);
            }
        }

        $compositeConstraint = new self();

        $compositeConstraint->type = $type;
        $compositeConstraint->constraints = $constraints;

        return $compositeConstraint;
    }

    /**
     * @param array $constraints
     * @return self
     */
    public static function fromAndConstraints(array $constraints)
    {
        return self::fromProperties(self::TYPE_AND, $constraints);
    }

    /**
     * @param array $constraints
     * @return self
     */
    public static function fromOrConstraints(array $constraints)
    {
        return self::fromProperties(self::TYPE_OR, $constraints);
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
     * {@inheritdoc}
     */
    public function assert(Version $version)
    {
        if ($this->type == self::TYPE_AND) {
            return $this->assertAnd($version);
        }

        return $this->assertOr($version);
    }

    protected function assertAnd(Version $version)
    {
        foreach ($this->constraints as $constraint) {
            /* @var $constraint ConstraintInterface */

            if (!$constraint->assert($version)) {
                return false;
            }
        }

        return true;
    }

    protected function assertOr(Version $version)
    {
        foreach ($this->constraints as $constraint) {
            /* @var $constraint ConstraintInterface */

            if ($constraint->assert($version)) {
                return true;
            }
        }

        return false;
    }
}
