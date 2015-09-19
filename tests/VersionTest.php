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
class VersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getNormalVersionsSet
     */
    public function testCreationFromNormalVersionString($versionString, $major, $minor, $patch)
    {
        $version = Version::fromString($versionString);

        $this->assertEquals($version->getMajor(), $major);
        $this->assertEquals($version->getMinor(), $minor);
        $this->assertEquals($version->getPatch(), $patch);
    }

    /**
     * @expectedException \Version\Exception\InvalidVersionStringException
     */
    public function testCreationFromStringFailsInCaseOfLeadingZeros()
    {
        Version::fromString('1.05.2');
    }

    /**
     * @expectedException \Version\Exception\InvalidArgumentException
     */
    public function testCreationFailsInCaseOfInvalidMajorVersion()
    {
        new Version('test', 1, 1);
    }

    /**
     * @expectedException \Version\Exception\InvalidArgumentException
     */
    public function testCreationFailsInCaseOfInvalidMinorVersion()
    {
        new Version(0, -5, 1);
    }

    /**
     * @expectedException \Version\Exception\InvalidArgumentException
     */
    public function testCreationFailsInCaseOfInvalidPatchVersion()
    {
        new Version(2, 1, 'patch');
    }

    /**
     * @dataProvider getComparisonSet
     */
    public function testVersionComparison($v1, $v2, $result)
    {
        $version1 = Version::fromString($v1);
        $version2 = Version::fromString($v2);

        static $resultMethodMap = [
            -1 => 'isLesserThan',
            0 => 'isEqualTo',
            1 => 'isGreaterThan'
        ];

        $method = $resultMethodMap[$result];

        $this->assertTrue($version1->$method($version2));
    }

    public function testVersionPrinting()
    {
        $this->assertEquals('2.1.0', (string) (new Version(2, 1, 0)));
        $this->assertEquals('1.0.0+20150919', (string) Version::fromString('1.0.0+20150919'));
        $this->assertEquals('1.0.0+exp.sha.5114f85', (string) Version::fromString('1.0.0+exp.sha.5114f85'));
        $this->assertEquals('1.0.0-alpha.1+exp.sha.5114f85', (string) Version::fromString('1.0.0-alpha.1+exp.sha.5114f85'));
    }

    public static function getNormalVersionsSet()
    {
        return array(
            array('0.9.7', 0, 9, 7),
            array('1.10.0', 1, 10, 0),
            array('2.5.4', 2, 5, 4),
            array('2.1.17', 2, 1, 17),
        );
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
