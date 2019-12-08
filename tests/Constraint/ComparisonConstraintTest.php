<?php

declare(strict_types=1);

namespace Version\Tests\Constraint;

use PHPUnit\Framework\TestCase;
use Version\Constraint\ComparisonConstraint;
use Version\Version;
use Version\Exception\InvalidComparisonConstraintException;

class ComparisonConstraintTest extends TestCase
{
    /**
     * @test
     */
    public function it_is_created_from_operator_and_operand(): void
    {
        $operand = Version::fromString('1.0.0');
        $constraint = new ComparisonConstraint(ComparisonConstraint::OPERATOR_GT, $operand);

        $this->assertInstanceOf(ComparisonConstraint::class, $constraint);
        $this->assertSame(ComparisonConstraint::OPERATOR_GT, $constraint->getOperator());
        $this->assertSame($operand, $constraint->getOperand());
    }

    /**
     * @test
     */
    public function it_raises_exception_if_operator_is_not_valid(): void
    {
        try {
            new ComparisonConstraint('invalid', Version::fromString('1.0.0'));

            $this->fail('Exception should have been raised');
        } catch (InvalidComparisonConstraintException $ex) {
            $this->assertSame('Unsupported comparison constraint operator: invalid', $ex->getMessage());
        }
    }

    /**
     * @test
     * @dataProvider getConstraintAssertions
     *
     * @param Version $version
     * @param ComparisonConstraint $constraint
     */
    public function it_asserts_provided_version(Version $version, ComparisonConstraint $constraint): void
    {
        $this->assertTrue($constraint->assert($version));
    }

    public function getConstraintAssertions(): array
    {
        return [
            [
                Version::fromString('1.0.0'),
                new ComparisonConstraint(ComparisonConstraint::OPERATOR_EQ, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('2.0.0'),
                new ComparisonConstraint(ComparisonConstraint::OPERATOR_NEQ, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.1.0'),
                new ComparisonConstraint(ComparisonConstraint::OPERATOR_GT, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                new ComparisonConstraint(ComparisonConstraint::OPERATOR_GTE, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                new ComparisonConstraint(ComparisonConstraint::OPERATOR_LT, Version::fromString('2.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                new ComparisonConstraint(ComparisonConstraint::OPERATOR_LTE, Version::fromString('1.0.0'))
            ],
        ];
    }
}
