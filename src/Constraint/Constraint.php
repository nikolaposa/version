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
use Version\Exception\InvalidConstraintException;
use Version\Constraint\Parser\ParserInterface;
use Version\Constraint\Parser\StandardParser;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class Constraint implements ConstraintInterface
{
    const OPERATOR_EQ = '=';
    const OPERATOR_NEQ = '!=';
    const OPERATOR_GT = '>';
    const OPERATOR_GTE = '>=';
    const OPERATOR_LT = '<';
    const OPERATOR_LTE = '<=';

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

    private function __construct($operator, Version $operand)
    {
        $this->operator = $operator;
        $this->operand = $operand;
    }

    /**
     * @param string $operator
     * @param Version $operand
     * @return self
     */
    public static function fromProperties($operator, Version $operand)
    {
        if (!self::isOperatorValid($operator)) {
            throw InvalidConstraintException::forOperator($operator);
        }

        return new self($operator, $operand);
    }

    protected static function isOperatorValid($operator)
    {
        return in_array($operator, self::$validOperators);
    }

    /**
     * @param string $constraintString
     * @return self
     */
    public static function fromString($constraintString)
    {
        return self::getParser()->parse($constraintString);
    }

    /**
     * @return ParserInterface
     */
    public static function getParser()
    {
        if (!isset(self::$parser)) {
            self::setParser(new StandardParser());
        }

        return self::$parser;
    }

    /**
     * @param ParserInterface $parser
     * @return void
     */
    public static function setParser(ParserInterface $parser)
    {
        self::$parser = $parser;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return Version
     */
    public function getOperand()
    {
        return $this->operand;
    }

    /**
     * {@inheritDoc}
     */
    public function assert(Version $version)
    {
        switch ($this->operator) {
            case self::OPERATOR_EQ :
                return $version->isEqualTo($this->operand);
            case self::OPERATOR_NEQ :
                return !$version->isEqualTo($this->operand);
            case self::OPERATOR_GT :
                return $version->isGreaterThan($this->operand);
            case self::OPERATOR_GTE :
                return $version->isGreaterOrEqualTo($this->operand);
            case self::OPERATOR_LT :
                return $version->isLessThan($this->operand);
            case self::OPERATOR_LTE :
                return $version->isLessOrEqualTo($this->operand);
        }
    }
}
