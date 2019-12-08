<?php

declare(strict_types=1);

namespace Version\Tests\Comparison\Constraint;

use PHPUnit\Framework\TestCase;
use Version\Comparison\Constraint\OperatorConstraint;
use Version\Version;
use Version\Comparison\Exception\InvalidOperatorConstraint;

class OperatorConstraintTest extends TestCase
{
    /**
     * @test
     */
    public function it_is_created_from_operator_and_operand(): void
    {
        $operand = Version::fromString('1.0.0');
        $constraint = new OperatorConstraint(OperatorConstraint::OPERATOR_GT, $operand);

        $this->assertInstanceOf(OperatorConstraint::class, $constraint);
        $this->assertSame(OperatorConstraint::OPERATOR_GT, $constraint->getOperator());
        $this->assertSame($operand, $constraint->getOperand());
    }

    /**
     * @test
     */
    public function it_raises_exception_if_operator_is_not_valid(): void
    {
        try {
            new OperatorConstraint('invalid', Version::fromString('1.0.0'));

            $this->fail('Exception should have been raised');
        } catch (InvalidOperatorConstraint $ex) {
            $this->assertSame('Unsupported constraint operator: invalid', $ex->getMessage());
        }
    }

    /**
     * @test
     * @dataProvider getConstraintAssertions
     *
     * @param Version $version
     * @param OperatorConstraint $constraint
     */
    public function it_asserts_provided_version(Version $version, OperatorConstraint $constraint): void
    {
        $this->assertTrue($constraint->assert($version));
    }

    public function getConstraintAssertions(): array
    {
        return [
            [
                Version::fromString('1.0.0'),
                new OperatorConstraint(OperatorConstraint::OPERATOR_EQ, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('2.0.0'),
                new OperatorConstraint(OperatorConstraint::OPERATOR_NEQ, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.1.0'),
                new OperatorConstraint(OperatorConstraint::OPERATOR_GT, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                new OperatorConstraint(OperatorConstraint::OPERATOR_GTE, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                new OperatorConstraint(OperatorConstraint::OPERATOR_LT, Version::fromString('2.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                new OperatorConstraint(OperatorConstraint::OPERATOR_LTE, Version::fromString('1.0.0'))
            ],
        ];
    }
}
