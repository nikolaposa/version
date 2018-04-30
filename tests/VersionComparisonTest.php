<?php

declare(strict_types=1);

namespace Version\Tests;

use PHPUnit\Framework\TestCase;
use Version\Version;
use Version\Constraint\ComparisonConstraint;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionComparisonTest extends TestCase
{
    public static function assertVersionGreaterThan($expected, Version $actual)
    {
        self::assertTrue($actual->isGreaterThan($expected), "$actual is not greater than $expected");
    }

    public function testVersionCompareTo()
    {
        $this->assertEquals(1, Version::fromString('2.1.1')->compareTo(Version::fromString('2.1.0')));
    }

    public function testComparisonAcceptsVersionsAsStrings()
    {
        $this->assertEquals(0, Version::fromString('2.0.0')->compareTo('2.0.0'));
    }

    public function testVersionIsEqualComparison()
    {
        $this->assertTrue(Version::fromString('1.0.0')->isEqualTo('1.0.0'));
    }

    public function testVersionIsNotEqualComparison()
    {
        $this->assertTrue(Version::fromString('1.0.0')->isNotEqualTo('2.0.0'));
    }

    public function testVersionGreaterThanComparison()
    {
        $this->assertTrue(Version::fromString('1.0.1')->isGreaterThan('1.0.0'));
    }

    public function testVersionGreaterOrEqualComparison()
    {
        $this->assertTrue(Version::fromString('1.0.0')->isGreaterOrEqualTo('1.0.0'));
    }

    public function testVersionLessThanComparison()
    {
        $this->assertTrue(Version::fromString('1.0.1')->isLessThan('1.0.2'));
    }

    public function testVersionLessOrEqualComparison()
    {
        $this->assertTrue(Version::fromString('1.0.0')->isLessOrEqualTo('1.0.0'));
    }

    public function testMatchesConstraint()
    {
        $this->assertTrue(Version::fromString('1.1.0')->matches(ComparisonConstraint::fromString('>1.0.0')));
    }
}
