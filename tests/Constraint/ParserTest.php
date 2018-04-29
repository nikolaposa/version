<?php

declare(strict_types=1);

namespace Version\Tests\Constraint;

use PHPUnit\Framework\TestCase;
use Version\Constraint\Parser\ParserInterface;
use Version\Constraint\Parser\StandardParser;
use Version\Constraint\Constraint;
use Version\Constraint\CompositeConstraint;
use Version\Exception\InvalidConstraintStringException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class ParserTest extends TestCase
{
    /**
     * @var ParserInterface
     */
    protected $parser;

    protected function setUp()
    {
        $this->parser = new StandardParser();
    }

    public static function assertConstraint($operator, $operandString, Constraint $constraint)
    {
        self::assertEquals($operator, $constraint->getOperator());
        self::assertEquals($operandString, (string) $constraint->getOperand());
    }

    public static function assertCompositeConstraint($type, array $constraints, CompositeConstraint $constraint)
    {
        self::assertEquals($type, $constraint->getType());

        $actualConstraints = $constraint->getConstraints();

        foreach ($actualConstraints as $i => $constraint) {
            if ($constraint instanceof CompositeConstraint) {
                self::assertCompositeConstraint(
                    $constraints[$i]['type'],
                    $constraints[$i]['constraints'],
                    $constraint
                );
                continue;
            }

            self::assertConstraint(
                $constraints[$i]['operator'],
                $constraints[$i]['operand'],
                $constraint
            );
        }
    }

    public function testParsingSimpleConstraint()
    {
        $constraint = $this->parser->parse('>=1.2.0');

        $this->assertInstanceOf(Constraint::class, $constraint);
        $this->assertConstraint('>=', '1.2.0', $constraint);
    }

    public function testParsingConstraintWithoutOperatorShouldUseEqualAsOperator()
    {
        $constraint = $this->parser->parse('1.2.0');

        $this->assertInstanceOf(Constraint::class, $constraint);
        $this->assertConstraint('=', '1.2.0', $constraint);
    }

    public function testParsingRangeConstraint()
    {
        $constraint = $this->parser->parse('>=1.2.3 <1.3.0');

        $this->assertInstanceOf(CompositeConstraint::class, $constraint);
        $this->assertCompositeConstraint(
            CompositeConstraint::TYPE_AND,
            [
                ['operator' => '>=', 'operand' => '1.2.3'],
                ['operator' => '<', 'operand' => '1.3.0'],
            ],
            $constraint
        );
    }

    public function testParsingConstraintWithLogicalOperators()
    {
        $constraint = $this->parser->parse('>=1.0.0 <1.1.0 || >=1.2.0');

        $this->assertInstanceOf(CompositeConstraint::class, $constraint);
        $this->assertCompositeConstraint(
            CompositeConstraint::TYPE_OR,
            [
                [
                    'type' => CompositeConstraint::TYPE_AND,
                    'constraints' => [
                        ['operator' => '>=', 'operand' => '1.0.0'],
                        ['operator' => '<', 'operand' => '1.1.0'],
                    ]
                ],
                [
                    'type' => CompositeConstraint::TYPE_AND,
                    'constraints' => [
                        ['operator' => '>=', 'operand' => '1.2.0'],
                    ]
                ],
            ],
            $constraint
        );
    }

    public function testExceptionIsRaisedIfConstraintStringIsEmpty()
    {
        $this->expectException(
            InvalidConstraintStringException::class,
            'Constraint string must not be empty'
        );

        $this->parser->parse('  ');
    }

    public function testExceptionIsRaisedIfConstraintStringCannotBeParsed()
    {
        $this->expectException(InvalidConstraintStringException::class);

        $this->parser->parse('invalid');
    }

    public function testExceptionIsRaisedIfConstraintContainsOperatorThatCannotBeParsed()
    {
        $this->expectException(InvalidConstraintStringException::class);

        $this->parser->parse('"100');
    }

    public function testExceptionIsRaisedIfConstraintContainsVersionThatCannotBeParsed()
    {
        $this->expectException(InvalidConstraintStringException::class);

        $this->parser->parse('>100');
    }

    public function testExceptionIsRaisedIfConstraintStringContainsInvalidLogicalOperation()
    {
        $this->expectException(InvalidConstraintStringException::class);

        $this->parser->parse('>=1.0.0 <1.1.0 ||');
    }
}
