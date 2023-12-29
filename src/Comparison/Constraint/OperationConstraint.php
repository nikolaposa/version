<?php

declare(strict_types=1);

namespace Version\Comparison\Constraint;

use ReflectionClass;
use Version\Version;
use Version\Comparison\Exception\InvalidOperationConstraint;

class OperationConstraint implements Constraint
{
    public const OPERATOR_EQ = '=';
    public const OPERATOR_NEQ = '!=';
    public const OPERATOR_GT = '>';
    public const OPERATOR_GTE = '>=';
    public const OPERATOR_LT = '<';
    public const OPERATOR_LTE = '<=';

    protected string $operator;

    protected Version $operand;

    final public function __construct(string $operator, Version $operand)
    {
        $this->validateOperator($operator);

        $this->operator = $operator;
        $this->operand = $operand;
    }

    public static function equalTo(Version $operand): static
    {
        return new static(self::OPERATOR_EQ, $operand);
    }

    public static function notEqualTo(Version $operand): static
    {
        return new static(self::OPERATOR_NEQ, $operand);
    }

    public static function greaterThan(Version $operand): static
    {
        return new static(self::OPERATOR_GT, $operand);
    }

    public static function greaterOrEqualTo(Version $operand): static
    {
        return new static(self::OPERATOR_GTE, $operand);
    }

    public static function lessThan(Version $operand): static
    {
        return new static(self::OPERATOR_LT, $operand);
    }

    public static function lessOrEqualTo(Version $operand): static
    {
        return new static(self::OPERATOR_LTE, $operand);
    }

    public static function fromString(string $constraintString): CompositeConstraint|OperationConstraint
    {
        static $parser = null;

        if (null === $parser) {
            $parser = new OperationConstraintParser();
        }

        return $parser->parse($constraintString);
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getOperand(): Version
    {
        return $this->operand;
    }

    public function assert(Version $version): bool
    {
        return match ($this->operator) {
            self::OPERATOR_EQ => $version->isEqualTo($this->operand),
            self::OPERATOR_NEQ => !$version->isEqualTo($this->operand),
            self::OPERATOR_GT => $version->isGreaterThan($this->operand),
            self::OPERATOR_GTE => $version->isGreaterOrEqualTo($this->operand),
            self::OPERATOR_LT => $version->isLessThan($this->operand),
            self::OPERATOR_LTE => $version->isLessOrEqualTo($this->operand),
            default => throw InvalidOperationConstraint::unsupportedOperator($this->operator),
        };
    }

    protected function validateOperator(string $operator): void
    {
        static $validOperators = null;

        if ($validOperators === null) {
            $validOperators = (new ReflectionClass($this))->getConstants();
        }

        if (!in_array($operator, $validOperators, true)) {
            throw InvalidOperationConstraint::unsupportedOperator($operator);
        }
    }
}
