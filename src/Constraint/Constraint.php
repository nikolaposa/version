<?php

declare(strict_types=1);

namespace Version\Constraint;

use Version\Version;
use Version\Exception\InvalidConstraintException;
use Version\Constraint\Parser\ParserInterface;
use Version\Constraint\Parser\StandardParser;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class Constraint implements ConstraintInterface
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
    private static $validOperators = [
        self::OPERATOR_EQ,
        self::OPERATOR_NEQ,
        self::OPERATOR_GT,
        self::OPERATOR_GTE,
        self::OPERATOR_LT,
        self::OPERATOR_LTE,
    ];

    /**
     * @var ParserInterface
     */
    private static $parser;

    protected function __construct(string $operator, Version $operand)
    {
        $this->operator = $operator;
        $this->operand = $operand;
    }

    public static function fromProperties(string $operator, Version $operand) : Constraint
    {
        self::validateOperator($operator);

        return new self($operator, $operand);
    }

    public static function fromString(string $constraintString) : Constraint
    {
        return self::getParser()->parse($constraintString);
    }

    protected static function validateOperator(string $operator) : void
    {
        if (! in_array($operator, self::$validOperators, true)) {
            throw InvalidConstraintException::forOperator($operator);
        }
    }

    public static function getParser() : ParserInterface
    {
        if (null === self::$parser) {
            self::setParser(new StandardParser());
        }

        return self::$parser;
    }

    public static function setParser(ParserInterface $parser) : void
    {
        self::$parser = $parser;
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
            default:
                return false;
        }
    }
}
