<?php

declare(strict_types=1);

namespace Version\Tests\Comparison\Constraint;

use PHPUnit\Framework\TestCase;
use Version\Comparison\Constraint\OperationConstraint;
use Version\Comparison\Constraint\CompositeConstraint;
use Version\Comparison\Exception\InvalidConstraintString;

class OperationConstraintParsingTest extends TestCase
{
    /**
     * @test
     */
    public function it_parses_simple_constraint(): void
    {
        $constraint = OperationConstraint::fromString('>=1.2.0');

        $this->assertInstanceOf(OperationConstraint::class, $constraint);
        $this->assertSame('>=', $constraint->getOperator());
        $this->assertSame('1.2.0', $constraint->getOperand()->toString());
    }

    /**
     * @test
     */
    public function it_uses_equals_as_default_operator(): void
    {
        $constraint = OperationConstraint::fromString('1.2.0');

        $this->assertInstanceOf(OperationConstraint::class, $constraint);
        $this->assertSame('=', $constraint->getOperator());
        $this->assertSame('1.2.0', $constraint->getOperand()->toString());
    }

    /**
     * @test
     */
    public function it_parses_range_constraint(): void
    {
        $constraint = OperationConstraint::fromString('>=1.2.3 <1.3.0');

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
    public function it_parses_composite_constraints_that_include_logical_operators(): void
    {
        $constraint = OperationConstraint::fromString('>=1.0.0 <1.1.0 || >=1.2.0');

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
    public function it_validates_constraint_string_for_emptiness(): void
    {
        try {
            OperationConstraint::fromString('  ');

            $this->fail('Exception should have been raised');
        } catch (InvalidConstraintString $ex) {
            $this->assertSame('Comparision constraint string must not be empty', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_validates_constraint_string_operator(): void
    {
        try {
            OperationConstraint::fromString('"100');

            $this->fail('Exception should have been raised');
        } catch (InvalidConstraintString $ex) {
            $this->assertSame("Comparision constraint string: '\"100' is not valid and cannot be parsed", $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_validates_constraint_string_version_operand(): void
    {
        try {
            OperationConstraint::fromString('>100');

            $this->fail('Exception should have been raised');
        } catch (InvalidConstraintString $ex) {
            $this->assertSame("Comparision constraint string: '>100' is not valid and cannot be parsed", $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_validates_compound_constraint_string(): void
    {
        try {
            OperationConstraint::fromString('>=1.0.0 <1.1.0 ||');

            $this->fail('Exception should have been raised');
        } catch (InvalidConstraintString $ex) {
            $this->assertSame("Comparision constraint string: '>=1.0.0 <1.1.0 ||' is not valid and cannot be parsed", $ex->getMessage());
        }
    }

    public static function assertCompositeConstraintIsIdentical(CompositeConstraint $compositeConstraint, string $expectedOperator, array $expectedConstraints): void
    {
        self::assertSame($expectedOperator, $compositeConstraint->getOperator());

        foreach ($compositeConstraint->getConstraints() as $i => $constraint) {
            /* @var $constraint OperationConstraint */

            if ($constraint instanceof CompositeConstraint) {
                self::assertCompositeConstraintIsIdentical(
                    $constraint,
                    $expectedConstraints[$i]['operator'],
                    $expectedConstraints[$i]['constraints']
                );
                continue;
            }

            self::assertSame($expectedConstraints[$i]['operator'], $constraint->getOperator());
            self::assertSame($expectedConstraints[$i]['operand'], $constraint->getOperand()->toString());
        }
    }
}
