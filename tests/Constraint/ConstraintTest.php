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
use Version\Exception\InvalidConstraintStringException;
use Version\Constraint\CompositeConstraint;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class ConstraintTest extends PHPUnit_Framework_TestCase
{
    public function testCreatingValidConstraint()
    {
        $operand = Version::fromString('1.0.0');
        $constraint = Constraint::create(Constraint::OPERATOR_GT, $operand);

        $this->assertEquals(Constraint::OPERATOR_GT, $constraint->getOperator());
        $this->assertSame($operand, $constraint->getOperand());
    }

    public function testExceptionIsRaisedInCaseOfInvalidOperator()
    {
        $this->setExpectedException(
            InvalidConstraintException::class,
            'Unsupported operator: invalid'
        );

        Constraint::create('invalid', Version::fromString('1.0.0'));
    }

    /**
     * @dataProvider constraintStrings
     */
    /*public function testCreatingConstraintFromString($constraintString, $operator, $operand)
    {
        $constraint = Constraint::fromString($constraintString);

        if (is_array($operator)) { //composite?
            $this->assertInstanceOf(CompositeConstraint::class, $constraint);

            return;
        }

        $this->assertEquals($operator, $constraint->getOperator());
        $this->assertEquals($operand, (string) $constraint->getOperand());
    }*/

    public static function constraintStrings()
    {
        return [
            ['>=1.0', '>=', '1.0.0'],
            [
                '>=1.0 <2.0',
                [
                    'type' => 'AND',
                    'constraints' => [
                        ['operator' => '>=', 'operand' => '1.0.0'],
                        ['operator' => '<', 'operand' => '2.0.0'],
                    ]
                ],
                null
            ],
            [
                '>=1.0 <1.1 || >=1.2',
                [
                    'type' => 'OR',
                    'constraints' => [
                        [
                            'type' => 'AND',
                            'constraints' => [
                                ['operator' => '>=', 'operand' => '1.0.0'],
                                ['operator' => '<', 'operand' => '1.1.0'],
                            ]
                        ],
                        ['operator' => '>=', 'operand' => '1.2.0'],
                    ]
                ],
                null
            ],
            [
                '>=1.0.0 <=2.1.0',
                [
                    'type' => 'AND',
                    'constraints' => [
                        ['operator' => '>=', 'operand' => '1.0.0'],
                        ['operator' => '<=', 'operand' => '2.1.0'],
                    ]
                ],
                null
            ],
            [
                '>=1.2.3 <1.3.0',
                [
                    'type' => 'AND',
                    'constraints' => [
                        ['operator' => '>=', 'operand' => '1.2.3'],
                        ['operator' => '<', 'operand' => '1.3.0'],
                    ]
                ],
                null
            ],
            ['>=1.2-beta', '>=', '1.2.0-beta'],
        ];
    }

    public function testExceptionIsRaisedIfConstraintStringIsNotString()
    {
        $this->setExpectedException(
            InvalidConstraintStringException::class,
            'Constraint string should be of type string; integer given'
        );

        Constraint::fromString(123);
    }

    public function testExceptionIsRaisedIfConstraintStringIsEmpty()
    {
        $this->setExpectedException(
            InvalidConstraintStringException::class,
            'Constraint string must not be empty'
        );

        Constraint::fromString('  ');
    }

    public function testExceptionIsRaisedIfConstraintStringCannotBeParsed()
    {
        $this->setExpectedException(InvalidConstraintStringException::class);

        Constraint::fromString('invalid');
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
                Constraint::create('=', Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('2.0.0'),
                Constraint::create('!=', Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.1.0'),
                Constraint::create('>', Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                Constraint::create('>=', Version::fromString('1.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                Constraint::create('<', Version::fromString('2.0.0'))
            ],
            [
                Version::fromString('1.0.0'),
                Constraint::create('<=', Version::fromString('1.0.0'))
            ],
        ];
    }
}
