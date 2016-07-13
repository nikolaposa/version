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
use Version\Constraint\Constraint;
use Version\Version;
use Version\Exception\InvalidConstraintException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class ConstraintTest extends PHPUnit_Framework_TestCase
{
    public function testCreatingConstraintUsingFromPropertiesNamedConstructor()
    {
        $operand = Version::fromString('1.0.0');
        $constraint = Constraint::fromProperties(Constraint::OPERATOR_GT, $operand);

        $this->assertInstanceOf(Constraint::class, $constraint);
        $this->assertEquals(Constraint::OPERATOR_GT, $constraint->getOperator());
        $this->assertSame($operand, $constraint->getOperand());
    }

    public function testExceptionIsRaisedInCaseOfInvalidOperator()
    {
        $this->setExpectedException(
            InvalidConstraintException::class,
            'Unsupported operator: invalid'
        );

        Constraint::fromProperties('invalid', Version::fromString('1.0.0'));
    }

    /**
     * @dataProvider constraintAssertions
     */
    public function testAssertingConstraint(Version $version, Constraint $constraint)
    {
        $this->assertTrue($constraint->assert($version));
    }

    public function constraintAssertions()
    {
        return [
            [
                Version::fromString('1.0.0'),
                Constraint::fromProperties(Constraint::OPERATOR_EQ, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('2.0.0'),
                Constraint::fromProperties(Constraint::OPERATOR_NEQ, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.1.0'),
                Constraint::fromProperties(Constraint::OPERATOR_GT, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                Constraint::fromProperties(Constraint::OPERATOR_GTE, Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                Constraint::fromProperties(Constraint::OPERATOR_LT, Version::fromString('2.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                Constraint::fromProperties(Constraint::OPERATOR_LTE, Version::fromString('1.0.0'))
            ],
        ];
    }
}
