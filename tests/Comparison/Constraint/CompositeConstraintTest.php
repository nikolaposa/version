<?php

declare(strict_types=1);

namespace Version\Tests\Comparison\Constraint;

use PHPUnit\Framework\TestCase;
use Version\Comparison\Constraint\CompositeConstraint;
use Version\Comparison\Constraint\OperationConstraint;
use Version\Version;
use Version\Comparison\Exception\InvalidCompositeConstraint;

class CompositeConstraintTest extends TestCase
{
    /**
     * @test
     */
    public function it_is_created_from_logical_operator_and_constraints(): void
    {
        $constraint = new CompositeConstraint(CompositeConstraint::OPERATOR_AND, ...[
            new OperationConstraint(OperationConstraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            new OperationConstraint(OperationConstraint::OPERATOR_LT, Version::fromString('1.1.0')),
        ]);

        $this->assertInstanceOf(CompositeConstraint::class, $constraint);
        $this->assertSame(CompositeConstraint::OPERATOR_AND, $constraint->getOperator());
        $this->assertCount(2, $constraint->getConstraints());
    }

    /**
     * @test
     */
    public function it_is_created_via_and_named_constructor(): void
    {
        $constraint = CompositeConstraint::and(...[
            new OperationConstraint(OperationConstraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            new OperationConstraint(OperationConstraint::OPERATOR_LT, Version::fromString('1.1.0')),
        ]);

        $this->assertSame(CompositeConstraint::OPERATOR_AND, $constraint->getOperator());
    }

    /**
     * @test
     */
    public function it_is_created_via_or_named_constructor(): void
    {
        $constraint = CompositeConstraint::or(...[
            new OperationConstraint(OperationConstraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            new OperationConstraint(OperationConstraint::OPERATOR_LT, Version::fromString('1.1.0')),
        ]);

        $this->assertSame(CompositeConstraint::OPERATOR_OR, $constraint->getOperator());
    }

    /**
     * @test
     */
    public function it_validates_operator_input(): void
    {
        try {
            new CompositeConstraint('invalid', ...[
                new OperationConstraint(OperationConstraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            ]);

            $this->fail('Exception should have been raised');
        } catch (InvalidCompositeConstraint $ex) {
            $this->assertSame('Unsupported composite constraint operator: invalid', $ex->getMessage());
        }
    }

    /**
     * @test
     */
    public function it_asserts_and_operation_constraint(): void
    {
        $constraint = CompositeConstraint::and(...[
            new OperationConstraint(OperationConstraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            new OperationConstraint(OperationConstraint::OPERATOR_LT, Version::fromString('1.1.0')),
        ]);

        $this->assertFalse($constraint->assert(Version::fromString('0.8.7')));
        $this->assertTrue($constraint->assert(Version::fromString('1.0.0')));
        $this->assertTrue($constraint->assert(Version::fromString('1.0.5')));
        $this->assertFalse($constraint->assert(Version::fromString('1.1.0')));
    }

    /**
     * @test
     */
    public function it_asserts_or_operation_constraint(): void
    {
        $constraint = CompositeConstraint::or(...[
            new OperationConstraint(OperationConstraint::OPERATOR_EQ, Version::fromString('4.7.1')),
            new OperationConstraint(OperationConstraint::OPERATOR_EQ, Version::fromString('5.0.0')),
        ]);

        $this->assertTrue($constraint->assert(Version::fromString('4.7.1')));
        $this->assertTrue($constraint->assert(Version::fromString('5.0.0')));
        $this->assertFalse($constraint->assert(Version::fromString('1.1.0')));
    }
}
