<?php

declare(strict_types=1);

namespace Version\Comparison\Constraint;

use Version\Version;
use Version\Comparison\Exception\InvalidCompositeConstraint;

class CompositeConstraint implements Constraint
{
    public const OPERATOR_AND = 'AND';
    public const OPERATOR_OR = 'OR';

    /** @var string */
    protected $operator;

    /** @var Constraint[] */
    protected $constraints;

    final public function __construct(string $operator, Constraint $constraint, Constraint ...$constraints)
    {
        if (! in_array($operator, [self::OPERATOR_AND, self::OPERATOR_OR], true)) {
            throw InvalidCompositeConstraint::unsupportedOperator($operator);
        }

        $this->operator = $operator;
        $this->constraints = array_merge([$constraint], $constraints);
    }

    public static function and(Constraint $constraint, Constraint ...$constraints): CompositeConstraint
    {
        return new static(self::OPERATOR_AND, $constraint, ...$constraints);
    }

    public static function or(Constraint $constraint, Constraint ...$constraints): CompositeConstraint
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
