<?php

declare(strict_types=1);

namespace Version\Constraint;

use Version\Version;
use Version\Exception\InvalidCompositeConstraintException;

class CompositeConstraint implements ConstraintInterface
{
    public const OPERATOR_AND = 'AND';
    public const OPERATOR_OR = 'OR';

    /** @var string */
    protected $operator;

    /** @var ConstraintInterface[] */
    protected $constraints;

    public function __construct(string $operator, ConstraintInterface $constraint, ConstraintInterface ...$constraints)
    {
        if (! in_array($operator, [self::OPERATOR_AND, self::OPERATOR_OR], true)) {
            throw InvalidCompositeConstraintException::forUnsupportedOperator($operator);
        }

        $this->operator = $operator;
        $this->constraints = array_merge([$constraint], $constraints);
    }

    public static function and(ConstraintInterface $constraint, ConstraintInterface ...$constraints): CompositeConstraint
    {
        return new static(self::OPERATOR_AND, $constraint, ...$constraints);
    }

    public static function or(ConstraintInterface $constraint, ConstraintInterface ...$constraints): CompositeConstraint
    {
        return new static(self::OPERATOR_OR, $constraint, ...$constraints);
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function assert(Version $version): bool
    {
        if ($this->operator === self::OPERATOR_AND) {
            return $this->assertAnd($version);
        }

        return $this->assertOr($version);
    }

    protected function assertAnd(Version $version): bool
    {
        foreach ($this->constraints as $constraint) {
            if (! $constraint->assert($version)) {
                return false;
            }
        }

        return true;
    }

    protected function assertOr(Version $version): bool
    {
        foreach ($this->constraints as $constraint) {
            if ($constraint->assert($version)) {
                return true;
            }
        }

        return false;
    }
}
