<?php

declare(strict_types=1);

namespace Version\Tests\Constraint;

use PHPUnit\Framework\TestCase;
use Version\Constraint\ComparisonConstraint;
use Version\Constraint\CompositeConstraint;
use Version\Exception\InvalidComparisonConstraintStringException;

class ComparisonConstraintParsingTest extends TestCase
{
    /**
     * @test
     */
    public function it_parses_simple_constraint() : void
    {
        $constraint = ComparisonConstraint::fromString('>=1.2.0');

        $this->assertInstanceOf(ComparisonConstraint::class, $constraint);
        $this->assertSame('>=', $constraint->getOperator());
        $this->assertSame('1.2.0', (string) $constraint->getOperand());
    }

    /**
     * @test
     */
    public function it_assumes_equals_as_default_operator_if_operator_not_supplied() : void
    {
        $constraint = ComparisonConstraint::fromString('1.2.0');

        $this->assertInstanceOf(ComparisonConstraint::class, $constraint);
        $this->assertSame('=', $constraint->getOperator());
        $this->assertSame('1.2.0', (string) $constraint->getOperand());
    }

    /**
     * @test
     */
    public function it_parses_range_constraint() : void
    {
        $constraint = ComparisonConstraint::fromString('>=1.2.3 <1.3.0');

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
    public function it_parses_composite_constraints_containing_logical_operators() : void
    {
        $constraint = ComparisonConstraint::fromString('>=1.0.0 <1.1.0 || >=1.2.0');

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
    public function it_raises_exception_if_the_constraint_string_is_empty() : void
    {
        try {
            ComparisonConstraint::fromString('  ');

            $this->fail('Exception should have been raised');
        } catch (InvalidComparisonConstraintStringException $ex) {
            $this->assertSame('Constraint string must not be empty', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_if_the_constraint_string_cannot_be_parsed() : void
    {
        try {
            ComparisonConstraint::fromString('invalid');

            $this->fail('Exception should have been raised');
        } catch (InvalidComparisonConstraintStringException $ex) {
            $this->assertSame("Constraint string: 'invalid' seems to be invalid and it cannot be parsed", $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_if_the_constraint_contains_operator_that_cannot_be_parsed() : void
    {
        try {
            ComparisonConstraint::fromString('"100');

            $this->fail('Exception should have been raised');
        } catch (InvalidComparisonConstraintStringException $ex) {
            $this->assertSame("Constraint string: '\"100' seems to be invalid and it cannot be parsed", $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_if_the_constraint_contains_version_that_cannot_be_parsed() : void
    {
        try {
            ComparisonConstraint::fromString('>100');

            $this->fail('Exception should have been raised');
        } catch (InvalidComparisonConstraintStringException $ex) {
            $this->assertSame("Constraint string: '>100' seems to be invalid and it cannot be parsed", $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_raises_exception_if_the_constraint_contains_invalid_logical_operation() : void
    {
        try {
            ComparisonConstraint::fromString('>=1.0.0 <1.1.0 ||');

            $this->fail('Exception should have been raised');
        } catch (InvalidComparisonConstraintStringException $ex) {
            $this->assertSame("Constraint string: '>=1.0.0 <1.1.0 ||' seems to be invalid and it cannot be parsed", $ex->getMessage());
        }
    }

    public static function assertCompositeConstraintIsIdentical(CompositeConstraint $compositeConstraint, string $expectedOperator, array $expectedConstraints)
    {
        self::assertSame($expectedOperator, $compositeConstraint->getOperator());

        foreach ($compositeConstraint->getConstraints() as $i => $constraint) {
            /* @var $constraint \Version\Constraint\ComparisonConstraint */

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
