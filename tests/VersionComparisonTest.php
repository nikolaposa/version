<?php

/**
 * This file is part of the Version package.
 *
 * Copyright (c) Nikola Posa <posa.nikola@gmail.com>
 *
 * For full copyright and license information, please refer to the LICENSE file,
 * located at the package root folder.
 */

namespace Version\Tests;

use PHPUnit_Framework_TestCase;
use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class VersionComparisonTest extends PHPUnit_Framework_TestCase
{
    private function assertVersionEqual($expected, Version $actual)
    {
        $this->assertTrue($actual->isEqualTo($expected), "$actual is not equal to $expected");
    }

    private function assertVersionGreaterThan($expected, Version $actual)
    {
        $this->assertTrue($actual->isGreaterThan($expected), "$actual is not greater than $expected");
    }

    private function assertVersionLessThan($expected, Version $actual)
    {
        $this->assertTrue($actual->isLessThan($expected), "$actual is not less than $expected");
    }

    public function testComparisonAcceptsVersionsAsStrings()
    {
        $this->assertVersionGreaterThan('2.1.0', Version::fromString('2.1.1'));
    }

    /**
     * @dataProvider versionsCompareList
     */
    public function testVersionCompareTo($version1, $version2, $result)
    {
        $this->assertSame($result, Version::fromString($version1)->compareTo($version2));
    }

    /**
     * @dataProvider versionsCompareList
     */
    public function testVersionComparison($version1, $version2, $result)
    {
        if ($result > 0) {
            $this->assertVersionGreaterThan($version2, Version::fromString($version1));
        } elseif ($result < 0) {
            $this->assertVersionLessThan($version2, Version::fromString($version1));
        } else {
            $this->assertVersionEqual($version2, Version::fromString($version1));
        }
    }

    public static function versionsCompareList()
    {
        return [
            ['2.1.1', '2.1.0', 1],
            ['1.10.1', '2.1.0', -1],
            ['1.0.0', '1.0.0', 0],
            ['1.0.0', '1.0.0-alpha', 1],
            ['1.0.0-alpha', '1.0.0-beta', -1],
            ['1.0.0-alpha.1', '1.0.0-alpha', 1],
            ['1.0.0-alpha.1', '1.0.0-alpha', 1],
            ['1.0.0-alpha.beta', '1.0.0-alpha.1', 1],
            ['1.0.0-beta', '1.0.0-alpha.beta', 1],
            ['1.0.0-beta.11', '1.0.0-beta.2', 1],
            ['1.0.0-rc.1', '1.0.0-beta.11', 1],
            ['1.0.0', '1.0.0-rc.1', 1],
            ['1.0.0-alpha+20150919', '1.0.0-alpha+exp.sha.5114f85', 0],
        ];
    }

    public function testVersionGreaterOrEqualComparison()
    {
        $this->assertTrue(Version::fromString('1.0.0')->isGreaterOrEqualTo('1.0.0'));
    }

    public function testVersionLessOrEqualComparison()
    {
        $this->assertTrue(Version::fromString('1.0.0')->isLessOrEqualTo('1.0.0'));
    }
}
