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
    public function testCreatingValidConstraint()
    {
        $operand = Version::fromString('1.0.0');
        $constraint = Constraint::fromProperties(Constraint::OPERATOR_GT, $operand);

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
                Constraint::fromProperties('=', Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('2.0.0'),
                Constraint::fromProperties('!=', Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.1.0'),
                Constraint::fromProperties('>', Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                Constraint::fromProperties('>=', Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                Constraint::fromProperties('<', Version::fromString('2.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                Constraint::fromProperties('<=', Version::fromString('1.0.0'))
            ],
        ];
    }
}
