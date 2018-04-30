<?php

declare(strict_types=1);

namespace Version\Tests\Constraint;

use PHPUnit\Framework\TestCase;
use Version\Constraint\ComparisonConstraint;
use Version\Version;
use Version\Exception\InvalidComparisonConstraintException;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class ComparisonConstraintTest extends TestCase
{
    public function testCreatingConstraint()
    {
        $operand = Version::fromString('1.0.0');
        $constraint = new ComparisonConstraint(ComparisonConstraint::OPERATOR_GT, $operand);

        $this->assertInstanceOf(ComparisonConstraint::class, $constraint);
        $this->assertEquals(ComparisonConstraint::OPERATOR_GT, $constraint->getOperator());
        $this->assertSame($operand, $constraint->getOperand());
    }

    public function testExceptionIsRaisedInCaseOfInvalidOperator()
    {
        $this->expectException(InvalidComparisonConstraintException::class);

        new ComparisonConstraint('invalid', Version::fromString('1.0.0'));
    }

    /**
     * @dataProvider constraintAssertions
     */
    public function testAssertingConstraint(Version $version, ComparisonConstraint $constraint)
    {
        $this->assertTrue($constraint->assert($version));
    }

    public function constraintAssertions() : array
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
