<?php

declare(strict_types=1);

namespace Version\Constraint;

use Version\Version;
use Version\Exception\InvalidComparisonConstraintException;

/** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class ComparisonConstraint implements ConstraintInterface
{
    public const OPERATOR_EQ = '=';
    public const OPERATOR_NEQ = '!=';
    public const OPERATOR_GT = '>';
    public const OPERATOR_GTE = '>=';
    public const OPERATOR_LT = '<';
    public const OPERATOR_LTE = '<=';

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var Version
     */
    protected $operand;

    /**
     * @var array
     */
    protected static $validOperators = [
        self::OPERATOR_EQ,
        self::OPERATOR_NEQ,
        self::OPERATOR_GT,
        self::OPERATOR_GTE,
        self::OPERATOR_LT,
        self::OPERATOR_LTE,
    ];

    public function __construct(string $operator, Version $operand)
    {
        if (! in_array($operator, static::$validOperators, true)) {
            throw InvalidComparisonConstraintException::forUnsupportedOperator($operator);
        }

        $this->operator = $operator;
        $this->operand = $operand;
    }

    public static function fromString(string $constraintString) : ConstraintInterface
    {
        static $parser = null;

        if (null === $parser) {
            $parser = new ComparisonConstraintParser();
        }

        return $parser->parse($constraintString);
    }

    public function getOperator() : string
    {
        return $this->operator;
    }

    public function getOperand() : Version
    {
        return $this->operand;
    }

    public function assert(Version $version) : bool
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
}
