<?php

declare(strict_types=1);

namespace Version\Tests\Constraint;

use PHPUnit\Framework\TestCase;
use Version\Constraint\ComparisonConstraint;
use Version\Constraint\CompositeConstraint;
use Version\Exception\InvalidComparisonConstraintStringException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class ComparisonConstraintParsingTest extends TestCase
{
    public static function assertConstraint($operator, $operandString, ComparisonConstraint $constraint)
    {
        self::assertEquals($operator, $constraint->getOperator());
        self::assertEquals($operandString, (string) $constraint->getOperand());
    }

    public static function assertCompositeConstraint($type, array $constraints, CompositeConstraint $compositeConstraint)
    {
        self::assertEquals($type, $compositeConstraint->getType());

        foreach ($compositeConstraint->getConstraints() as $i => $constraint) {
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
        $constraint = ComparisonConstraint::fromString('>=1.2.0');

        $this->assertConstraint('>=', '1.2.0', $constraint);
    }

    public function testParsingConstraintWithoutOperatorShouldUseEqualAsOperator()
    {
        $constraint = ComparisonConstraint::fromString('1.2.0');

        $this->assertConstraint('=', '1.2.0', $constraint);
    }

    public function testParsingRangeConstraint()
    {
        $constraint = ComparisonConstraint::fromString('>=1.2.3 <1.3.0');

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
        $constraint = ComparisonConstraint::fromString('>=1.0.0 <1.1.0 || >=1.2.0');

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
        $this->expectException(InvalidComparisonConstraintStringException::class);
        $this->expectExceptionMessage('Constraint string must not be empty');

        ComparisonConstraint::fromString('  ');
    }

    public function testExceptionIsRaisedIfConstraintStringCannotBeParsed()
    {
        $this->expectException(InvalidComparisonConstraintStringException::class);

        ComparisonConstraint::fromString('invalid');
    }

    public function testExceptionIsRaisedIfConstraintContainsOperatorThatCannotBeParsed()
    {
        $this->expectException(InvalidComparisonConstraintStringException::class);

        ComparisonConstraint::fromString('"100');
    }

    public function testExceptionIsRaisedIfConstraintContainsVersionThatCannotBeParsed()
    {
        $this->expectException(InvalidComparisonConstraintStringException::class);

        ComparisonConstraint::fromString('>100');
    }

    public function testExceptionIsRaisedIfConstraintStringContainsInvalidLogicalOperation()
    {
        $this->expectException(InvalidComparisonConstraintStringException::class);

        ComparisonConstraint::fromString('>=1.0.0 <1.1.0 ||');
    }
}
