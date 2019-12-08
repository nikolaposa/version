<?php

declare(strict_types=1);

namespace Version\Comparison\Constraint;

use ReflectionClass;
use Version\Version;
use Version\Exception\InvalidOperatorConstraintException;

class OperatorConstraint implements Constraint
{
    public const OPERATOR_EQ = '=';
    public const OPERATOR_NEQ = '!=';
    public const OPERATOR_GT = '>';
    public const OPERATOR_GTE = '>=';
    public const OPERATOR_LT = '<';
    public const OPERATOR_LTE = '<=';

    /** @var string */
    protected $operator;

    /** @var Version */
    protected $operand;

    public function __construct(string $operator, Version $operand)
    {
        $this->validateOperator($operator);

        $this->operator = $operator;
        $this->operand = $operand;
    }

    /**
     * @param string $constraintString
     * @return OperatorConstraint|CompositeConstraint
     */
    public static function fromString(string $constraintString)
    {
        static $parser = null;

        if (null === $parser) {
            $parser = new OperatorConstraintParser();
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
        switch ($this->operator) {
            case self::OPERATOR_EQ:
                return $version->isEqualTo($this->operand);
            case self::OPERATOR_NEQ:
                return !$version->isEqualTo($this->operand);
            case self::OPERATOR_GT:
                return $version->isGreaterThan($this->operand);
            case self::OPERATOR_GTE:
                return $version->isGreaterOrEqualTo($this->operand);
            case self::OPERATOR_LT:
                return $version->isLessThan($this->operand);
            case self::OPERATOR_LTE:
                return $version->isLessOrEqualTo($this->operand);
        }
    }

    protected function validateOperator($operator): void
    {
        static $validOperators = null;

        if (null === $validOperators) {
            $validOperators = (new ReflectionClass($this))->getConstants();
        }

        if (! in_array($operator, $validOperators, true)) {
            throw InvalidOperatorConstraintException::forUnsupportedOperator($operator);
        }
    }
}
