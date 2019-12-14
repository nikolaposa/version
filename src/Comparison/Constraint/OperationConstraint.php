<?php

declare(strict_types=1);

namespace Version\Comparison\Constraint;

use BadMethodCallException;
use ReflectionClass;
use Version\Version;
use Version\Comparison\Exception\InvalidOperationConstraint;

/**
 * @method static OperationConstraint equalTo(Version $operand)
 * @method static OperationConstraint notEqualTo(Version $operand)
 * @method static OperationConstraint greaterThan(Version $operand)
 * @method static OperationConstraint greaterOrEqualTo(Version $operand)
 * @method static OperationConstraint lessThan(Version $operand)
 * @method static OperationConstraint lessOrEqualTo(Version $operand)
 */
class OperationConstraint implements Constraint
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

    public static function __callStatic($name, $arguments)
    {
        $methodNameOperatorMap = [
            'equalTo' => self::OPERATOR_EQ,
            'notEqualTo' => self::OPERATOR_NEQ,
            'greaterThan' => self::OPERATOR_GT,
            'greaterOrEqualTo' => self::OPERATOR_GTE,
            'lessThan' => self::OPERATOR_LT,
            'lessOrEqualTo' => self::OPERATOR_LTE,
        ];

        if (!isset($methodNameOperatorMap[$name])) {
            throw new BadMethodCallException("Operation OperationConstraint::$name is not supported");
        }

        if (!isset($arguments[0])) {
            throw new BadMethodCallException('Operand is missing');
        }

        return new static($methodNameOperatorMap[$name], $arguments[0]);
    }

    /**
     * @param string $constraintString
     * @return OperationConstraint|CompositeConstraint
     */
    public static function fromString(string $constraintString)
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
            throw InvalidOperationConstraint::unsupportedOperator($operator);
        }
    }
}
