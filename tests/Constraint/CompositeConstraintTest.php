<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Tests\Constraint;

use PHPUnit_Framework_TestCase;
use Version\Constraint\CompositeConstraint;
use Version\Constraint\Constraint;
use Version\Version;
use Version\Exception\InvalidCompositeConstraintException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class CompositeConstraintTest extends PHPUnit_Framework_TestCase
{
    public function testCreatingCompositeConstraintUsingFromPropertiesNamedConstructor()
    {
        $constraint = CompositeConstraint::fromProperties(CompositeConstraint::TYPE_AND, [
            Constraint::fromProperties(Constraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            Constraint::fromProperties(Constraint::OPERATOR_LT, Version::fromString('1.1.0')),
        ]);

        $this->assertInstanceOf(CompositeConstraint::class, $constraint);
        $this->assertEquals(CompositeConstraint::TYPE_AND, $constraint->getType());
        $this->assertCount(2, $constraint->getConstraints());
    }

    public function testCreatingCompositeConstraintUsingFromAndConstraintsNamedConstructor()
    {
        $constraint = CompositeConstraint::fromAndConstraints([
            Constraint::fromProperties(Constraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            Constraint::fromProperties(Constraint::OPERATOR_LT, Version::fromString('1.1.0')),
        ]);

        $this->assertInstanceOf(CompositeConstraint::class, $constraint);
        $this->assertEquals(CompositeConstraint::TYPE_AND, $constraint->getType());
    }

    public function testCreatingCompositeConstraintUsingFromOrConstraintsNamedConstructor()
    {
        $constraint = CompositeConstraint::fromOrConstraints([
            Constraint::fromProperties(Constraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            Constraint::fromProperties(Constraint::OPERATOR_LT, Version::fromString('1.1.0')),
        ]);

        $this->assertInstanceOf(CompositeConstraint::class, $constraint);
        $this->assertEquals(CompositeConstraint::TYPE_OR, $constraint->getType());
    }

    public function testExceptionIsRaisedInCaseOfInvalidType()
    {
        $this->setExpectedException(
            InvalidCompositeConstraintException::class,
            'Unsupported type: invalid'
        );

        CompositeConstraint::fromProperties('invalid', [
            Constraint::fromProperties(Constraint::OPERATOR_GTE, Version::fromString('1.0.0')),
        ]);
    }

    public function testExceptionIsRaisedInCaseOfInvalidConstraint()
    {
        $this->setExpectedException(InvalidCompositeConstraintException::class);

        CompositeConstraint::fromProperties(CompositeConstraint::TYPE_AND, [
            Constraint::fromProperties(Constraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            'invalid',
        ]);
    }

    public function testAssertingCompositeConstraintOfAndType()
    {
        $constraint = CompositeConstraint::fromAndConstraints([
            Constraint::fromProperties(Constraint::OPERATOR_GTE, Version::fromString('1.0.0')),
            Constraint::fromProperties(Constraint::OPERATOR_LT, Version::fromString('1.1.0')),
        ]);

        $this->assertFalse($constraint->assert(Version::fromString('0.8.7')));
        $this->assertTrue($constraint->assert(Version::fromString('1.0.0')));
        $this->assertTrue($constraint->assert(Version::fromString('1.0.5')));
        $this->assertFalse($constraint->assert(Version::fromString('1.1.0')));
    }

    public function testAssertingCompositeConstraintOfOrType()
    {
        $constraint = CompositeConstraint::fromOrConstraints([
            Constraint::fromProperties(Constraint::OPERATOR_EQ, Version::fromString('4.7.1')),
            Constraint::fromProperties(Constraint::OPERATOR_EQ, Version::fromString('5.0.0')),
        ]);

        $this->assertTrue($constraint->assert(Version::fromString('4.7.1')));
        $this->assertTrue($constraint->assert(Version::fromString('5.0.0')));
        $this->assertFalse($constraint->assert(Version::fromString('1.1.0')));
    }
}
