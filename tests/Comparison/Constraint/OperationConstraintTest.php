<?php

declare(strict_types=1);

namespace Version\Tests\Comparison\Constraint;

use BadMethodCallException;
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
     */
    public function it_validates_named_constructor_operator(): void
    {
        try {
            /** @noinspection PhpUndefinedMethodInspection */
            OperationConstraint::invalid(Version::fromString('1.0.0'));

            $this->fail('Exception should have been raised');
        } catch (BadMethodCallException $ex) {
            $this->assertSame('Operation OperationConstraint::invalid is not supported', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_validates_named_constructor_operand(): void
    {
        try {
            /** @noinspection PhpParamsInspection */
            OperationConstraint::equalTo();

            $this->fail('Exception should have been raised');
        } catch (BadMethodCallException $ex) {
            $this->assertSame('Operand is missing', $ex->getMessage());
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

    public function getConstraintAssertions(): array
    {
        return [
            [
                Version::fromString('1.0.0'),
                new OperationConstraint(OperationConstraint::OPERATOR_EQ, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('2.0.0'),
                new OperationConstraint(OperationConstraint::OPERATOR_NEQ, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.1.0'),
                new OperationConstraint(OperationConstraint::OPERATOR_GT, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                new OperationConstraint(OperationConstraint::OPERATOR_GTE, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                new OperationConstraint(OperationConstraint::OPERATOR_LT, Version::fromString('2.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                new OperationConstraint(OperationConstraint::OPERATOR_LTE, Version::fromString('1.0.0'))
            ],
        ];
    }
}
