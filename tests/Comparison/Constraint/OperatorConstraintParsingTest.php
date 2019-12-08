<?php

declare(strict_types=1);

namespace Version\Tests\Comparison\Constraint;

use PHPUnit\Framework\TestCase;
use Version\Comparison\Constraint\OperatorConstraint;
use Version\Comparison\Constraint\CompositeConstraint;
use Version\Comparison\Exception\InvalidConstraintString;

class OperatorConstraintParsingTest extends TestCase
{
    /**
     * @test
     */
    public function it_parses_simple_constraint(): void
    {
        $constraint = OperatorConstraint::fromString('>=1.2.0');

        $this->assertInstanceOf(OperatorConstraint::class, $constraint);
        $this->assertSame('>=', $constraint->getOperator());
        $this->assertSame('1.2.0', (string) $constraint->getOperand());
    }

    /**
     * @test
     */
    public function it_assumes_equals_as_default_operator_if_operator_not_supplied(): void
    {
        $constraint = OperatorConstraint::fromString('1.2.0');

        $this->assertInstanceOf(OperatorConstraint::class, $constraint);
        $this->assertSame('=', $constraint->getOperator());
        $this->assertSame('1.2.0', (string) $constraint->getOperand());
    }

    /**
     * @test
     */
    public function it_parses_range_constraint(): void
    {
        $constraint = OperatorConstraint::fromString('>=1.2.3 <1.3.0');

        $this->assertInstanceOf(CompositeConstraint::class, $constraint);
        $this->assertCompositeConstraintIsIdentical(
            $constraint,
            CompositeConstraint::OPERATOR_AND,
            [
                ['operator' => '>=', 'operand' => '1.2.3'],
                ['operator' => '<', 'operand' => '1.3.0'],
            ]
        );
    }

    /**
     * @test
     */
    public function it_parses_composite_constraints_containing_logical_operators(): void
    {
        $constraint = OperatorConstraint::fromString('>=1.0.0 <1.1.0 || >=1.2.0');

        $this->assertInstanceOf(CompositeConstraint::class, $constraint);
        $this->assertCompositeConstraintIsIdentical(
            $constraint,
            CompositeConstraint::OPERATOR_OR,
            [
                [
                    'operator' => CompositeConstraint::OPERATOR_AND,
                    'constraints' => [
                        ['operator' => '>=', 'operand' => '1.0.0'],
                        ['operator' => '<', 'operand' => '1.1.0'],
                    ]
                ],
                [
                    'operator' => CompositeConstraint::OPERATOR_AND,
                    'constraints' => [
                        ['operator' => '>=', 'operand' => '1.2.0'],
                    ]
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function it_raises_exception_if_the_constraint_string_is_empty(): void
    {
        try {
            OperatorConstraint::fromString('  ');

            $this->fail('Exception should have been raised');
        } catch (InvalidConstraintString $ex) {
            $this->assertSame('Comparision constraint string must not be empty', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_if_the_constraint_string_cannot_be_parsed(): void
    {
        try {
            OperatorConstraint::fromString('invalid');

            $this->fail('Exception should have been raised');
        } catch (InvalidConstraintString $ex) {
            $this->assertSame("Comparision constraint string: 'invalid' is not valid and cannot be parsed", $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_if_the_constraint_contains_operator_that_cannot_be_parsed(): void
    {
        try {
            OperatorConstraint::fromString('"100');

            $this->fail('Exception should have been raised');
        } catch (InvalidConstraintString $ex) {
            $this->assertSame("Comparision constraint string: '\"100' is not valid and cannot be parsed", $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_if_the_constraint_contains_version_that_cannot_be_parsed(): void
    {
        try {
            OperatorConstraint::fromString('>100');

            $this->fail('Exception should have been raised');
        } catch (InvalidConstraintString $ex) {
            $this->assertSame("Comparision constraint string: '>100' is not valid and cannot be parsed", $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_if_the_constraint_contains_invalid_logical_operation(): void
    {
        try {
            OperatorConstraint::fromString('>=1.0.0 <1.1.0 ||');

            $this->fail('Exception should have been raised');
        } catch (InvalidConstraintString $ex) {
            $this->assertSame("Comparision constraint string: '>=1.0.0 <1.1.0 ||' is not valid and cannot be parsed", $ex->getMessage());
        }
    }

    public static function assertCompositeConstraintIsIdentical(CompositeConstraint $compositeConstraint, string $expectedOperator, array $expectedConstraints)
    {
        self::assertSame($expectedOperator, $compositeConstraint->getOperator());

        foreach ($compositeConstraint->getConstraints() as $i => $constraint) {
            /* @var $constraint \Version\Comparison\Constraint\OperatorConstraint */

            if ($constraint instanceof CompositeConstraint) {
                self::assertCompositeConstraintIsIdentical(
                    $constraint,
                    $expectedConstraints[$i]['operator'],
                    $expectedConstraints[$i]['constraints']
                );
                continue;
            }

            self::assertSame($expectedConstraints[$i]['operator'], $constraint->getOperator());
            self::assertSame($expectedConstraints[$i]['operand'], (string) $constraint->getOperand());
        }
    }
}
