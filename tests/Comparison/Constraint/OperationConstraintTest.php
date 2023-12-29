<?php

declare(strict_types=1);

namespace Version\Tests\Comparison\Constraint;

use PHPUnit\Framework\TestCase;
use Version\Comparison\Constraint\OperationConstraint;
use Version\Version;
use Version\Comparison\Exception\InvalidOperationConstraint;

class OperationConstraintTest extends TestCase
{
    /**
     * @test
     */
    public function it_is_created_from_operator_and_operand(): void
    {
        $operand = Version::fromString('1.0.0');
        $constraint = new OperationConstraint(OperationConstraint::OPERATOR_GT, $operand);

        $this->assertInstanceOf(OperationConstraint::class, $constraint);
        $this->assertSame(OperationConstraint::OPERATOR_GT, $constraint->getOperator());
        $this->assertSame($operand, $constraint->getOperand());
    }

    /**
     * @test
     */
    public function it_is_created_via_named_constructor(): void
    {
        $operand = Version::fromString('1.2.3');
        $constraint = OperationConstraint::equalTo($operand);

        $this->assertInstanceOf(OperationConstraint::class, $constraint);
        $this->assertSame(OperationConstraint::OPERATOR_EQ, $constraint->getOperator());
        $this->assertSame($operand, $constraint->getOperand());
    }

    /**
     * @test
     */
    public function it_validates_operator_input(): void
    {
        try {
            new OperationConstraint('invalid', Version::fromString('1.0.0'));

            $this->fail('Exception should have been raised');
        } catch (InvalidOperationConstraint $ex) {
            $this->assertSame('Unsupported constraint operator: invalid', $ex->getMessage());
        }
    }

    /**
     * @test
     * @dataProvider getConstraintAssertions
     *
     * @param Version $version
     * @param OperationConstraint $constraint
     */
    public function it_asserts_provided_version(Version $version, OperationConstraint $constraint): void
    {
        $this->assertTrue($constraint->assert($version));
    }

    public static function getConstraintAssertions(): array
    {
        return [
            [
                Version::fromString('1.0.0'),
                OperationConstraint::equalTo(Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('2.0.0'),
                OperationConstraint::notEqualTo(Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.1.0'),
                OperationConstraint::greaterThan(Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                OperationConstraint::greaterOrEqualTo(Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                OperationConstraint::lessThan(Version::fromString('2.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                OperationConstraint::lessOrEqualTo(Version::fromString('1.0.0'))
            ],
        ];
    }
}
