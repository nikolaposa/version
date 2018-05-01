<?php

declare(strict_types=1);

namespace Version\Tests\Constraint;

use PHPUnit\Framework\TestCase;
use Version\Constraint\CompositeConstraint;
use Version\Constraint\ComparisonConstraint;
use Version\Version;
use Version\Exception\InvalidCompositeConstraintException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class CompositeConstraintTest extends TestCase
{
    public function testCreatingCompositeConstraintUsingFromPropertiesNamedConstructor()
    {
        $constraint = new CompositeConstraint(CompositeConstraint::TYPE_AND, ...[
            new ComparisonConstraint(ComparisonConstraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            new ComparisonConstraint(ComparisonConstraint::OPERATOR_LT, Version::fromString('1.1.0')),
        ]);

        $this->assertInstanceOf(CompositeConstraint::class, $constraint);
        $this->assertEquals(CompositeConstraint::TYPE_AND, $constraint->getType());
        $this->assertCount(2, $constraint->getConstraints());
    }

    public function testCreatingCompositeConstraintUsingFromAndConstraintsNamedConstructor()
    {
        $constraint = CompositeConstraint::and(...[
            new ComparisonConstraint(ComparisonConstraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            new ComparisonConstraint(ComparisonConstraint::OPERATOR_LT, Version::fromString('1.1.0')),
        ]);

        $this->assertInstanceOf(CompositeConstraint::class, $constraint);
        $this->assertEquals(CompositeConstraint::TYPE_AND, $constraint->getType());
    }

    public function testCreatingCompositeConstraintUsingFromOrConstraintsNamedConstructor()
    {
        $constraint = CompositeConstraint::or(...[
            new ComparisonConstraint(ComparisonConstraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            new ComparisonConstraint(ComparisonConstraint::OPERATOR_LT, Version::fromString('1.1.0')),
        ]);

        $this->assertInstanceOf(CompositeConstraint::class, $constraint);
        $this->assertEquals(CompositeConstraint::TYPE_OR, $constraint->getType());
    }

    public function testExceptionIsRaisedInCaseOfInvalidType()
    {
        try {
            new CompositeConstraint('invalid', ...[
                new ComparisonConstraint(ComparisonConstraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            ]);

            $this->fail('Exception should have been raised');
        } catch (InvalidCompositeConstraintException $ex) {
            $this->assertSame('Unsupported composite constraint type: invalid', $ex->getMessage());
        }
    }

    public function testAssertingCompositeConstraintOfAndType()
    {
        $constraint = CompositeConstraint::and(...[
            new ComparisonConstraint(ComparisonConstraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            new ComparisonConstraint(ComparisonConstraint::OPERATOR_LT, Version::fromString('1.1.0')),
        ]);

        $this->assertFalse($constraint->assert(Version::fromString('0.8.7')));
        $this->assertTrue($constraint->assert(Version::fromString('1.0.0')));
        $this->assertTrue($constraint->assert(Version::fromString('1.0.5')));
        $this->assertFalse($constraint->assert(Version::fromString('1.1.0')));
    }

    public function testAssertingCompositeConstraintOfOrType()
    {
        $constraint = CompositeConstraint::or(...[
            new ComparisonConstraint(ComparisonConstraint::OPERATOR_EQ, Version::fromString('4.7.1')),
            new ComparisonConstraint(ComparisonConstraint::OPERATOR_EQ, Version::fromString('5.0.0')),
        ]);

        $this->assertTrue($constraint->assert(Version::fromString('4.7.1')));
        $this->assertTrue($constraint->assert(Version::fromString('5.0.0')));
        $this->assertFalse($constraint->assert(Version::fromString('1.1.0')));
    }
}
