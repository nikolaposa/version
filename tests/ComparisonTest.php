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

use Version\Version;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class ComparisonTest extends \PHPUnit_Framework_TestCase
{
    protected function assertVersionEqual(Version $expected, Version $actual)
    {
        $this->assertTrue($actual->isEqualTo($expected), "$actual is not equal to $expected");
    }

    protected function assertVersionGreaterThan(Version $expected, Version $actual)
    {
        $this->assertTrue($actual->isGreaterThan($expected), "$actual is not greater than $expected");
    }

    protected function assertVersionLessThan(Version $expected, Version $actual)
    {
        $this->assertTrue($actual->isLessThan($expected), "$actual is not less than $expected");
    }

    /**
     * @dataProvider getComparisonSet
     */
    public function testVersionComparison($v1, $v2, $result)
    {
        $version1 = Version::fromString($v1);
        $version2 = Version::fromString($v2);

        if ($result > 0) {
            $this->assertVersionGreaterThan($version2, $version1);
        } elseif ($result < 0) {
            $this->assertVersionLessThan($version2, $version1);
        } else {
            $this->assertVersionEqual($version2, $version1);
        }
    }

    public static function getComparisonSet()
    {
        return array(
            array('2.1.1', '2.1.0', 1),
            array('1.10.1', '2.1.0', -1),
            array('1.0.0', '1.0.0', 0),
            array('1.0.0', '1.0.0-alpha', 1),
            array('1.0.0-alpha', '1.0.0-beta', -1),
            array('1.0.0-alpha.1', '1.0.0-alpha', 1),
            array('1.0.0-alpha.1', '1.0.0-alpha', 1),
            array('1.0.0-alpha.beta', '1.0.0-alpha.1', 1),
            array('1.0.0-beta', '1.0.0-alpha.beta', 1),
            array('1.0.0-beta.11', '1.0.0-beta.2', 1),
            array('1.0.0-rc.1', '1.0.0-beta.11', 1),
            array('1.0.0', '1.0.0-rc.1', 1),
            array('1.0.0-alpha+20150919', '1.0.0-alpha+exp.sha.5114f85', 0),
        );
    }
}
